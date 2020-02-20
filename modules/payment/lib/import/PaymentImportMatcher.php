<?php

namespace payment\import;

use payment\service\PaymentImportService;
use payment\model\PaymentImportLine;
use base\service\CustomerService;
use invoice\service\InvoiceService;
use payment\service\PaymentService;
use core\exception\ObjectNotFoundException;

class PaymentImportMatcher {
    
    protected $paymentImportId;
    
    protected $customerService;
    
    protected $invoiceService;
    protected $invoicePrefix = '';
    protected $invoiceNumberLengths = null;
    
    public function __construct() {
//         $this->paymentImportId = $paymentImportId;
        
        $this->customerService = object_container_get(CustomerService::class);
        
        $this->invoicePrefix = \core\Context::getInstance()->getPrefixNumbers();
        $this->invoiceService = object_container_get(InvoiceService::class);
        $this->invoiceNumberLengths = $this->invoiceService->getInvoiceNumberLengths();
    }
    
    public function setPaymentImportId($id) { $this->paymentImportId = $id; }
    public function getPaymentImportId($id) { return $this->paymentImportId; }
    
    
    public function getUpdatedImportLines() {
        
    }
    
    
    /**
     * matchLine() - looks up customer + invoice for given import-line
     */
    public function matchLine($paymentImportLineId) {
        $piService = object_container_get(PaymentImportService::class);
        $pil = $piService->readImportLine( $paymentImportLineId );
        
        if (!$pil) {
            throw new ObjectNotFoundException('Line not found');
        }
        
        // lines to skip
        if ($pil->getImportStatus() == 'skip') {
            return false;
        }
        if ($pil->getImportStatus() == 'imported') {
            return false;
        }
        
        $customer = null;
        $invoice = null;
        
        // lookup customer or invoice
        if ($customer = $this->lookupCustomer( $pil )) {
            $invoice = $this->lookupInvoice($pil, $customer);
        }
        else if ($invoice = $this->lookupInvoice($pil)) {
        }
        else if ($customer = $this->lookupPreviousMatch($pil)) {
            $invoice = $this->lookupInvoice($pil, $customer);
        }
        
//         var_export($customer);exit;
        
        // no match?
        if ($customer == null && $invoice == null) {
            return false;
        }

        if ($customer) {
            if ($customer->getCompany()) {
                $pil->setCompanyId( $customer->getCompany()->getCompanyId() );
            } else if ($customer->getPerson()) {
                $pil->setPersonId( $customer->getPerson()->getPersonId() );
            }
        }
        if ($invoice) {
            $pil->setInvoiceId( $invoice->getInvoiceId() );
            $pil->setCompanyId( $invoice->getCompanyId() );
            $pil->setPersonId( $invoice->getPersonId() );
        }
        
        $piService->saveImportLine( $pil );
        
        $pil = $piService->readImportLine( $paymentImportLineId );
        
        return $pil;
    }
    
    /**
     * rematchPreviousLines() - looks if there are older unmatched lines for 
     *                          given bankaccount_contra in current batch
     */
    public function rematchPreviousLines(PaymentImportLine $pil) {
        
    }
    
    
    protected function lookupCustomer(PaymentImportLine $pil) {
        $customerService = object_container_get(CustomerService::class);
        
        $data = $pil->getFields();
        PaymentSheetImporter::normalizeRow($data);
        
        if (trim($data['bankaccountno_contra']) == '') {
            return null;
        }
        
        $listResponse = $customerService->search(0, 1, array('iban' => $data['bankaccountno_contra']));
        if ($listResponse->getRowCount() == 1) {
            $objs = $listResponse->getObjects();
            
            $company_id = null;
            $person_id = null;
            if ($objs[0]['type'] == 'company') {
                $company_id = $objs[0]['id'];
            }
            if ($objs[0]['type'] == 'person') {
                $person_id = $objs[0]['id'];
            }
            
            return $this->customerService->readCustomerAuto( $company_id, $person_id );
        }
        
        return null;
    }
    
    protected function lookupInvoice(PaymentImportLine $pil, $customer=null) {
        $desc = $pil->getDescription();
        
        $matches = array();
        
        if ($this->invoicePrefix) {
            preg_match('/'.preg_quote($this->invoicePrefix).'\\s*\\d+/', $desc, $matches);
        }
        if (count($matches) == 0) {
            preg_match('/\\d+/', $desc, $matches);
        }
        
        if (count($matches) == 0) {
            return;
        }
        
        foreach($matches as $possibleInvoiceNo) {
            if (in_array(strlen($possibleInvoiceNo), $this->invoiceNumberLengths) == false)
                continue;
            
            $invoice = $this->invoiceService->readInvoiceByNumber( $possibleInvoiceNo );
            if (!$invoice)
                continue;
            
            // check customer, if set
            if ($customer != null) {
                if ($customer->getCompany()) {
                    if ($invoice->getCompanyId() != $customer->getCompany()->getCompanyId()) {
                        continue;
                    }
                } else if ($customer->getPerson()) {
                    if ($invoice->getPersonId() != $customer->getPerson()->getPersonId()) {
                        continue;
                    }
                }
            }
            
            
            $total_invoice = $invoice->getTotalCalculatedPriceInclVat();
            if ($total_invoice == null) {
                // hmz.. this shouldn't happen
                $total_invoice = $invoice->getTotalAmountInclVat();
            }
            
            if ($total_invoice == $pil->getAmount()) {
                return $invoice;
            }
            
            // less then X% price difference? => probably same invoice
//             if ($price) {
//                 $diffp = abs( 1 - ($price / $invoice->getTotalCalculatedPriceInclVat()) ) * 100;
//                 if ($diffp < 0.5) {
//                      return $invoice;
//                 }
//             }
            
            // < 1 euro difference?
            $diffa = abs($total_invoice - $pil->getAmount());
            if ($diffa < 1) {
                return $invoice;
            }
        }
        
        return null;
    }
    
    
    
    protected function lookupPreviousMatch(PaymentImportLine $pil) {
        $paymentService = object_container_get(PaymentService::class);
        
        if (trim($pil->getBankaccountnoContra()) == '') {
            return null;
        }
        
        $opts = array();
        $opts['iban'] = $pil->getBankaccountnoContra();
        $opts['order'] = 'payment_import_line_id desc';
        $opts['matched_customer'] = true;
        
        $lr = $paymentService->searchPaymentLine(0, 10, $opts);
        if ($lr->getRowCount()) {
            
            $objs = $lr->getObjects();
            
            $obj = $objs[0];
            
            return $this->customerService->readCustomerAuto( $obj['company_id'], $obj['person_id'] );
        }
        
        
        return null;
    }
    
    
}

