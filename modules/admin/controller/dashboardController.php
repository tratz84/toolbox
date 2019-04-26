<?php



use admin\controller\AdminBaseController;
use admin\service\AdminCustomerService;

class dashboardController extends AdminBaseController {
    
    
    public function action_index() {
        
        $acService = $this->oc->get(AdminCustomerService::class);
        
        $this->customers = $acService->readCustomers();
        
        
        $this->render();
    }
    
}