<?php


use base\service\CustomerService;
use core\controller\BaseController;

class customerController extends BaseController {
    
    
    
    public function action_search() {
        $pageNo = isset($_REQUEST['pageNo']) ? (int)$_REQUEST['pageNo'] : 0;
        $limit = $this->ctx->getPageSize();
        
        $customerService = $this->oc->get(CustomerService::class);
        
        $r = $customerService->search($pageNo*$limit, $limit, $_REQUEST);
        
        $arr = array();
        $arr['listResponse'] = $r;
        
        
        $this->json($arr);
    }
    
    public function action_select2() {

        $customerService = $this->oc->get(CustomerService::class);
        
        $r = $customerService->search(0, 20, $_REQUEST);
        
        
        $arr = array();
        
        if (isset($_REQUEST['name']) == false || trim($_REQUEST['name']) == '') {
            $arr[] = array(
                'id' => '0',
                'text' => 'Maak uw keuze'
            );
        }
        foreach($r->getObjects() as $customer) {
            $arr[] = array(
                'id' => $customer['type'] . '-' . $customer['id'], 
                'text' => $customer['name']
            );
        }
        
        
        $result = array();
        $result['results'] = $arr;
        
        $this->json($result);
        
    }
    
}


