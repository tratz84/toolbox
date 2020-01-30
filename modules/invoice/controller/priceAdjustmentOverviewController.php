<?php



use core\controller\BaseController;
use invoice\service\InvoiceService;

class priceAdjustmentOverviewController extends BaseController {
    
    
    public function action_index() {
        
        $invoiceService = $this->oc->get(InvoiceService::class);
        
        $this->priceAdjustments = $invoiceService->readPriceAdjustments($this->refObject, $this->refId);
        
        $this->priceAdjustments = array_reverse($this->priceAdjustments);
        
        $this->render();
    }
    
    
}
