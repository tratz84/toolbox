<?php


use core\controller\BaseReportController;
use invoice\form\InvoiceTotalsForm;
use invoice\service\InvoiceService;

class invoiceTotalsController extends BaseReportController {
    
    
    public function report() {
        
        $this->form = new InvoiceTotalsForm();
        $this->form->bind($_REQUEST);
        
        
        $invoiceService = $this->oc->get(InvoiceService::class);
        $this->totals = $invoiceService->readInvoiceTotals( $_REQUEST );
        
        return $this->renderToString();
    }
    
}
