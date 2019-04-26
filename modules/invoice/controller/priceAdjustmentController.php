<?php



use core\controller\BaseController;
use invoice\service\InvoiceService;

class priceAdjustmentController extends BaseController {
    
    
    public function action_delete() {
        
        $invoiceService = $this->oc->get(InvoiceService::class);
        $invoiceService->deletePriceAdjustment($_REQUEST['id']);
        
        if (isset($_REQUEST['back_url'])) {
            redirect($_REQUEST['back_url']);
        } else {
            redirect('/');
        }
    }
    
}

