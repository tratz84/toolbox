<?php

use core\controller\BaseController;

class paymentOverviewController extends BaseController {
    
    
    
    public function action_index() {
        
        $params = array();
        
        
        if (isset($this->companyId) && $this->companyId)
            $params['company_id'] = $this->companyId;
            if (isset($this->personId) && $this->personId)
                $params['person_id'] = $this->personId;
                
                if (isset($this->refObject) && $this->refObject) {
                    $params['ref_object'] = $this->refObject;
                }
                if (isset($this->refId) && $this->refId) {
                    $params['ref_id'] = $this->refId;
                }
                
                if (count($params)) {
                    $this->params = $params;
                    
                    $this->setShowDecorator(false);
                    $this->render();
                }
    }
    
    
    public function action_search() {
        $pageNo = isset($_REQUEST['pageNo']) ? (int)$_REQUEST['pageNo'] : 0;
        $limit = $this->ctx->getPageSize();
        
        $paymentService = $this->oc->get(PaymentService::class);
        
        $r = $paymentService->search($pageNo*$limit, $limit, $_REQUEST);
        
        $arr = array();
        $arr['listResponse'] = $r;
        
        
        $this->json($arr);
    }

}

