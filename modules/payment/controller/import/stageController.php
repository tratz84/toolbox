<?php



use base\service\CustomerService;
use core\controller\BaseController;
use core\exception\InvalidStateException;
use core\parser\SheetReader;
use invoice\service\InvoiceService;
use payment\import\PaymentImportMatcher;
use payment\service\PaymentImportService;


class stageController extends BaseController {
    
    
    
    
    public function action_index() {
        $id = get_var('id');
        
        $piService = object_container_get(PaymentImportService::class);
        $this->pi = $piService->readImport($id);
        
        
        $ctx = \core\Context::getInstance();
        $this->prefixNumbers = $ctx->getPrefixNumbers();
        
        
        $this->lines = array();
        foreach($this->pi->getImportLines() as $pl) {
            if (get_var('incoming') == '1') {
                if ($pl->getAmount() < 0) continue;
            }
            
            $this->lines[] = $pl->asArray();
        }
        
        return $this->render();
    }
    
    
    public function action_update_customer() {
        $piService = object_container_get(PaymentImportService::class);
        
        $pil_id = get_var('payment_import_line_id');
        
        $cid = get_var('customer_id');
        $person_id = $company_id = null;
        if (strpos($cid, 'company-') === 0) {
            $company_id = (int)substr($cid, strlen('company-'));
        }
        if (strpos($cid, 'person-') === 0) {
            $person_id = (int)substr($cid, strlen('person-'));
        }
        
        $piService->setCustomer($pil_id, $company_id, $person_id);
        
//         $customerService = object_container_get(CustomerService::class);
        // Company OR Person
//         $customer = $customerService->readCustomerAuto($company_id, $person_id);
        
        $pil = $piService->readImportLine( $pil_id );
        
        $r = array();
        $r['success'] = true;
        $r['payment_import_lines'] = array( $pil->asArray() );
//         $r['name'] = $name;
//         $r['person_id'] = $customer->getPerson() ? $customer->getPerson()->getPersonId() : null;
//         $r['company_id'] = $customer->getCompany() ? $customer->getCompany()->getCompanyId() : null;
        
        return $this->json( $r );
    }

    public function action_update_invoice() {
        $piService = object_container_get(PaymentImportService::class);
        
        $pil_id = get_var('payment_import_line_id');
        
        $invoice_id = (int)get_var('invoice_id');
        if ($invoice_id == 0)
            $invoice_id = null;
        $piService->setInvoice($pil_id, $invoice_id);
        
        
        // build response
        $r = array();
        if ($invoice_id) {
            $invoiceService = object_container_get(InvoiceService::class);
            $invoice = $invoiceService->readInvoice( $invoice_id );
//             $r['invoice_number'] = $invoice->getInvoiceNumberText();
//             $r['invoice_id'] = $invoice->getInvoiceId();
            
//             $customerService = object_container_get(CustomerService::class);
//             $customer = $customerService->readCustomerAuto( $invoice->getCompanyId(), $invoice->getPersonId() );
//             if ($customer) {
//                 $r['name'] = format_customername($customer);
//                 if ($customer->getCompany()) {
//                     $r['company_id'] = $customer->getCompany()->getCompanyId();
//                 }
//                 if ($customer->getPerson()) {
//                     $r['person_id'] = $customer->getPerson()->getPersonId();
//                 }
//             }
            
            $pil = $piService->readImportLine( $pil_id );
            
            
            $r['success'] = true;
            $r['payment_import_lines'] = array( $pil->asArray() );
            
        } else {
            $r['success'] = false;
        }
        
        
        return $this->json( $r );
    }
    
    
    public function action_skip() {
        $piService = object_container_get(PaymentImportService::class);
        $pil_id = get_var('payment_import_line_id');
        
        $p = $piService->markSkipped($pil_id);
        
        $r = array();
        $r['success'] = true;
        $r['payment_import_lines'] = array(
            $p->asArray()
        );
        
        return $this->json( $r );
    }

    public function action_unskip() {
        $piService = object_container_get(PaymentImportService::class);
        $pil_id = get_var('payment_import_line_id');
        
        $p = $piService->markUnskipped($pil_id);
        
        $r = array();
        $r['success'] = true;
        $r['payment_import_lines'] = array(
            $p->asArray()
        );
        
        return $this->json( $r );
    }
    
    
    
    public function action_create() {
        $f = basename(get_var('f'));
        
        $fullpath = get_data_file_safe('/tmp/', $f);
        if ($fullpath == false) {
            throw new InvalidStateException('Sheet file not found');
        }
        
        
        $sr = new SheetReader( $fullpath );
        if ($sr->read() == false) {
            $this->error = 'Unable to read sheet';
            return $this->render();
        }
        
        
        $head = $sr->getRow(0);                         // fetch 1st row (head)
        $uq_sheet = md5( implode(',', $head) );         // unique key for sheet
        
        $f = get_data_file( '/payments/mapping-'.$uq_sheet );
        $mapping = @unserialize( file_get_contents($f) );
        
        if ($mapping == false || is_array($mapping) == false) {
            throw new InvalidStateException('Mapping not found');
        }
        
        
        $piService = object_container_get(PaymentImportService::class);
        $pi = $piService->stageImport( $fullpath, $mapping );
        
        redirect('/?m=payment&c=import/stage&id='.$pi->getPaymentImportId());
    }
    
    
    public function action_match_line() {
        $pim = new PaymentImportMatcher();
        
        $r = array();
        
        $pil = null;
        
        $piService = object_container_get(PaymentImportService::class);
        $pil = $piService->readImportLine( get_var('payment_import_line_id') );
        
        
        if ($pim->checkDuplicate( $pil )) {
            $r['success'] = false;
        }
        else if ($pim->matchLine( $pil )) {
            $r['success'] = true;
        } else {
            $r['success'] = false;
        }
        
        $pil = $piService->readImportLine( get_var('payment_import_line_id') );
        
        $r['payment_import_lines'] = array();
        if ($pil) {
            $r['payment_import_lines'][] = $pil->asArray();
        }
        
        return $this->json( $r );
    }
    
    
    public function action_import() {
        
        $piService = object_container_get(PaymentImportService::class);
        
        $r = array();
        try {
            $pil_id = get_var('payment_import_line_id');
            
            $payment = $piService->createPayment( $pil_id );
            
            $pil = $piService->readImportLine( $pil_id );
            
            $r['success'] = true;
            $r['payment_import_lines'] = array( $pil->asArray() );
        } catch (\Exception $ex) {
            $r['success'] = false;
            $r['message'] = $ex->getMessage();
        }
        
        return $this->json( $r );
    }
    
    
    
    public function action_done() {
        $piService = object_container_get(PaymentImportService::class);
        $piService->markBatchDone( get_var('id') );
        
        redirect('/?m=payment&c=import');
    }

    public function action_reopen() {
        $piService = object_container_get(PaymentImportService::class);
        $piService->reopenBatch( get_var('id') );
        
        redirect('/?m=payment&c=import/stage&id='.get_var('id'));
    }
    
    
}


