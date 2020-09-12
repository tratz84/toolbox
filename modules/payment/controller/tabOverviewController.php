<?php

use core\controller\BaseController;
use invoice\service\InvoiceService;
use payment\service\PaymentService;

class tabOverviewController extends BaseController {
    
    
    
    public function action_index() {
        
        $companyId = null;
        $personId = null;
        
        if (isset($this->companyId) && $this->companyId)
            $companyId = $this->params['company_id'] = $this->companyId;
        if (isset($this->personId) && $this->personId)
            $personId = $this->params['person_id'] = $this->personId;
        
        $paymentService = object_container_get( PaymentService::class );
        $this->paymentSummary = $paymentService->summaryByCustomer( $companyId, $personId );
        
        if (ctx()->isModuleEnabled('invoice')) {
            $invoiceService = object_container_get( InvoiceService::class );
            $this->invoiceSummary = $invoiceService->summaryByCustomer( $companyId, $personId );
            
            $this->diff_cents = $diff_cents = intval($this->invoiceSummary['sum_total_calculated_price_incl_vat']*100) - intval($this->paymentSummary['sum_amount']*100);
            if ($diff_cents > 0) {
                report_user_warning('Let op, openstaand bedrag '. format_price(myround($diff_cents/100,2)));
            }
            
        }
        
        
        
        if (count($this->params)) {
            $this->params['exploded'] = true;
            $this->setShowDecorator(false);
            $this->render();
        }
    }
    
}

