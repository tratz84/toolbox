<?php


use core\controller\BaseReportController;
use invoice\service\InvoiceService;
use payment\form\PaymentTotalsForm;
use payment\service\PaymentService;
use core\exception\InvalidStateException;

class paymentTotalsController extends BaseReportController {
    
    
    public function report() {

        $this->form = new PaymentTotalsForm();
        $this->form->bind($_REQUEST);
        
        
        
        $this->totalsByCustomer = array();
        
        $paymentService = object_container_get( PaymentService::class );
        $this->paymentTotals = $paymentService->readPaymentTotals( $_REQUEST );
        
        foreach($this->paymentTotals as $pt) {
            if ($pt['company_id']) {
                $cid = 'company-'.$pt['company_id'];
            }
            else if ($pt['person_id']) {
                $cid = 'person-'.$pt['person_id'];
            }
            else {
                throw new InvalidStateException('Record with no customer linked (1)');
            }
            
            if (isset($this->totalsByCustomer[$cid]) == false) {
                $this->totalsByCustomer[$cid] = array();
            }
            $this->totalsByCustomer[$cid] = array_merge($this->totalsByCustomer[$cid], $pt);
        }
        
        if (ctx()->isModuleEnabled('invoice')) {
            $invoiceService = object_container_get(InvoiceService::class);
            $this->invoiceTotals = $invoiceService->readInvoiceTotals( $_REQUEST );
            
            
            foreach($this->invoiceTotals as $it) {
                if ($it['company_id']) {
                    $cid = 'company-'.$it['company_id'];
                }
                else if ($it['person_id']) {
                    $cid = 'person-'.$it['person_id'];
                }
                else {
                    throw new InvalidStateException('Record with no customer linked (2)');
                }
                

                if (isset($this->totalsByCustomer[$cid]) == false) {
                    $this->totalsByCustomer[$cid] = array();
                }
                
                $this->totalsByCustomer[$cid] = array_merge($this->totalsByCustomer[$cid], $it);
            }
        }
        
        foreach($this->totalsByCustomer as $cid => $arr) {
            $diff_cents = intval(@$arr['sum_total_calculated_price_incl_vat']*100) - intval(@$arr['total_amount']*100);
            
            $this->totalsByCustomer[$cid]['diff_cents'] = $diff_cents;
            $this->totalsByCustomer[$cid]['open_amount'] = myround($diff_cents/100, 2);
        }
        
        
        $customerIds = array_keys( $this->totalsByCustomer );
        usort($customerIds, function($o1, $o2) {
            $n1 = format_customername( $this->totalsByCustomer[$o1] );
            $n2 = format_customername( $this->totalsByCustomer[$o2] );
            
            return strcasecmp($n1, $n2);
        });
        $this->customerIds = $customerIds;
        
        
        return $this->renderToString();
    }
    
}
