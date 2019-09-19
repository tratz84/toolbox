<?php


use core\controller\BaseController;
use fastsite\service\WebformService;

class webformsController extends BaseController {
    
    
    public function action_index() {
        
        
        return $this->render();
    }
    
    public function action_search() {
        $pageNo = isset($_REQUEST['pageNo']) ? (int)$_REQUEST['pageNo'] : 0;
        $limit = $this->ctx->getPageSize();
        
        $webformService = $this->oc->get(WebformService::class);
        
        $r = $webformService->searchForms($pageNo*$limit, $limit, $_REQUEST);
        
        $arr = array();
        $arr['listResponse'] = $r;
        
        
        $this->json($arr);
    }
    
    
    public function action_edit() {
        
        return $this->render();
    }
    
    
    public function action_delete() {
        
        
    }
    
    
}

