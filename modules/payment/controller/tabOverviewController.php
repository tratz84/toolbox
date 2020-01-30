<?php

use core\controller\BaseController;
use payment\service\PaymentService;

class tabOverviewController extends BaseController {
    
    
    
    public function action_index() {
        $params = array();
        
        if (isset($this->companyId) && $this->companyId)
            $params['company_id'] = $this->companyId;
        if (isset($this->personId) && $this->personId)
            $params['person_id'] = $this->personId;
            
        
        if (count($params)) {
            $this->params = $params;
            
            $this->setShowDecorator(false);
            $this->render();
        }
    }
    
    
    
}

