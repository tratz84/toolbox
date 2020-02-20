<?php

namespace payment\service;


use core\forms\lists\ListResponse;
use core\service\ServiceBase;
use payment\import\PaymentSheetImporter;
use payment\model\PaymentImport;
use payment\model\PaymentImportDAO;
use payment\model\PaymentImportLineDAO;
use core\exception\ObjectNotFoundException;
use invoice\service\InvoiceService;
use core\exception\InvalidStateException;
use payment\model\PaymentImportLine;
use payment\model\Payment;
use payment\model\PaymentLine;

class PaymentImportService extends ServiceBase {
    
    
    public function stageImport($file, $mapping) {
        
        $psi = new PaymentSheetImporter();
        $psi->setSheetFile( $file );
        $psi->setMapping( $mapping );
        $psi->parseSheet();
        
        $pi = new PaymentImport();
        $pi->setDescription('New import ' . basename($file));
        $pi->save();
        
        for($x=1; $x < $psi->getRowCount(); $x++) {
            $pil = $psi->createPaymentImportLine( $x );
            $pil->setPaymentImportId( $pi->getPaymentImportId() );
            
            $pil->save();
            
            $pi->addImportLine( $pil );
        }
        
        return $pi;
    }
    
    public function deleteImport($paymentImportId) {
        // throws ObjectNotFound-exception if not found
        $pi = $this->readImport( $paymentImportId );
        
        
        // delete
        $piDao = object_container_get(PaymentImportDAO::class);
        $pilDao = object_container_get(PaymentImportLineDAO::class);
        
        $pilDao->deleteByImport( $paymentImportId );
        $piDao->delete( $paymentImportId );
    }
    
    
    public function readImport($id) {
        $piDao = object_container_get(PaymentImportDAO::class);
        $pi = $piDao->read($id);
        
        if (!$pi) {
            throw new ObjectNotFoundException('PaymentImport not found');
        }
        
        $pilDao = object_container_get(PaymentImportLineDAO::class);
        $lines = $pilDao->readByImport( $pi->getPaymentImportId() );
        
        $pi->setImportLines($lines);
        
        return $pi;
    }
    
    
    public function searchImport($start, $limit, $opts = array()) {
        $piDao = new PaymentImportDAO();
        
        $cursor = $piDao->search($opts);
        
        $r = ListResponse::fillByCursor($start, $limit, $cursor, array('payment_import_id', 'description', 'created', 'count'));
        
        return $r;
    }
    
    public function checkDuplicate(PaymentImportLine $pil) {
        $piDao = new PaymentImportDAO();
        
        $pis = $piDao->readDuplicate($pil);
        
        return count($pis) > 0 ? true : false;
    }
    
    
    public function readImportLine($paymentImportLineId) {
        $pilDao = new PaymentImportLineDAO();
        
        $pil = $pilDao->read( $paymentImportLineId );
        
        return $pil;
    }
    
    
    public function saveImportLine(PaymentImportLine $pil) {
        $pil->save();
        
        
        
        return true;
    }
    
    
    
    public function setCustomer($paymentImportLineId, $companyId, $personId) {
        $pilDao = new PaymentImportLineDAO();
        
        $pil = $pilDao->read( $paymentImportLineId );
        
        if (!$pil) {
            throw new ObjectNotFoundException('PaymentImportLine not found');
        }
        
        $pil->setPersonId( null );
        $pil->setCompanyId( null );
        if ($companyId) {
            $pil->setCompanyId( $companyId );
        } else if ($personId) {
            $pil->setPersonId( $personId );
        }
        
        return $pil->save();
    }
    
    
    public function setInvoice($paymentImportLineId, $invoiceId) {
        // fetch PaymentImportLine
        $pil = $this->readImportLine( $paymentImportLineId );
        if (!$pil) {
            throw new ObjectNotFoundException('PaymentImportLine not found');
        }
        
        // fetch Invoice
        if ($invoiceId != null) {
            $invoiceService = object_container_get(InvoiceService::class);
            $invoice = $invoiceService->readInvoice( $invoiceId );
            
            if (!$invoice) {
                throw new ObjectNotFoundException('Invoice not found');
            }
            $pil->setInvoiceId( $invoice->getInvoiceId() );
        } else {
            $pil->setInvoiceId( null );
        }
        
        
        
        return $pil->save();
    }
    
    
    public function markSkipped($paymentImportLineId) {
        
        $pil = $this->readImportLine($paymentImportLineId);
        $pil->setImportStatus('skip');
        $pil->save();
        
        return $pil;
    }

    public function markUnskipped($paymentImportLineId) {
        $pil = $this->readImportLine( $paymentImportLineId );
        
        if ($pil->getImportStatus() != 'skip') {
            throw new InvalidStateException('Line not marked as skipped');
        }
        
        $pil->setImportStatus('ready');
        $pil->save();
        
        return $pil;
    }
    
    public function markDuplicate($paymentImportLineId) {
        $pil = $this->readImportLine( $paymentImportLineId );
        
        $pil->setImportStatus('duplicate');
        return $pil->save();
    }
    
    public function markImported($paymentImportLineId) {
        $pil = $this->readImportLine( $paymentImportLineId );
        
        if ($pil->getImportStatus() == 'imported') {
            throw new InvalidStateException('Line already imported');
        }
        
        $pil->setImportStatus('imported');
        $pil->save();
        
        return $pil;
    }
    
    
    public function createPayment( $paymentImportLineId ) {
        $pil = $this->readImportLine( $paymentImportLineId );
        
        if (!$pil) {
            throw new ObjectNotFoundException('Line not found');
        }
        
        if ($pil->getCompanyId() == null && $pil->getPersonId() == null) {
            throw new InvalidStateException('No customer linked');
        }

        $pi_id = $pil->getPaymentImportId();
        
        $piDao = object_container_get(PaymentImportDAO::class);
        $pi = $piDao->read($pi_id);
        
        if ($pi->getStatus() == 'done') {
            throw new InvalidStateException('Import-batch marked as done');
        }
        
        $pm_id = \core\Context::getInstance()->getSetting('payment_import_payment_method_id');
        if (!$pm_id) {
            throw new InvalidStateException('No payment method selected for batch-imports');
        }
        
        $data = $pil->getFields();
        PaymentSheetImporter::normalizeRow($data);
        
        $pl = new PaymentLine();
        $pl->setAmount($data['amount']);
        $pl->setBankaccountno($data['bankaccountno']);
        $pl->setBankaccountnoContra($data['bankaccountno_contra']);
        $pl->setPaymentMethodId($pm_id);
        $pl->setCode($data['code']);
        $pl->setName($data['name']);
        $pl->setDescription1($data['description']);
        $pl->setMutationType($data['mutation_type']);
        $pl->setSort(0);
        
        $p = new Payment();
        if (valid_date($data['payment_date'])) {
            $p->setPaymentDate( format_date($data['payment_date'], 'Y-m-d') );
        } else {
            $p->setPaymentDate(date('Y-m-d'));
        }
        $p->setCompanyId($pil->getCompanyId());
        $p->setPersonId($pil->getPersonId());
        $p->setAmount($pl->getAmount());
        
        $desc = 'Import #'.$pi->getPaymentImportId();
        if ($data['invoice_id']) {
            $invoiceService = object_container_get(InvoiceService::class);
            $invoice = $invoiceService->readInvoice( $data['invoice_id'] );
            if ($invoice) {
                $desc = $desc . ', Factuur: ' . $invoice->getInvoiceNumberText();
            }
        }
        
        $p->setDescription($desc);
        $p->save();
        
        // save line
        $pl->setPaymentId($p->getPaymentId());
        $pl->save();
        
        $this->markImported($pil->getPaymentImportLineId() );
        
        
        return $p;
    }
    
    
}

