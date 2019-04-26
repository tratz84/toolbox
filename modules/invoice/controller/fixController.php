<?php


use core\controller\BaseController;
use invoice\service\InvoiceService;
use invoice\model\InvoiceDAO;

class fixController extends BaseController {
    
    
    public function action_index() {
        
    }
    
    
    
    public function action_fix_invoice_totals() {
        
        if ($this->ctx->getUser()->getUserType() != 'admin')
            die('Not an admin');
        
        $is = object_container_get(InvoiceService::class);
        
        $lr = $is->searchInvoice(0, 99999);
        
        $iDao = new InvoiceDAO();
        foreach($lr->getObjects() as $o) {
            
            $invoice = $is->readInvoice( $o['invoice_id'] );
            
            print "Updating invoice: " . $invoice->getInvoiceNumberText() . "<br/>\n";
            $iDao->updateField($invoice->getInvoiceId(), 'total_calculated_price', $invoice->getTotalAmountExclVat());
            $iDao->updateField($invoice->getInvoiceId(), 'total_calculated_price_incl_vat', $invoice->getTotalAmountInclVat());
        }
    }
    
}