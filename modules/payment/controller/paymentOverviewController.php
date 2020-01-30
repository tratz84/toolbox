<?php

use core\controller\BaseController;
use payment\service\PaymentService;

class paymentOverviewController extends BaseController {
    
    
    
    public function action_index() {
        
        
        $this->render();
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

