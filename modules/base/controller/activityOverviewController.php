<?php



use base\service\ActivityService;
use core\controller\BaseController;

class activityOverviewController extends BaseController {
    
    
    public function action_index() {
        
        if (isset($this->companyId) == false)
            $this->companyId = 0;
        if (isset($this->personId) == false)
            $this->personId = 0;
        
        
        $this->setShowDecorator(false);
        $this->render();
    }
    
    
    public function action_search() {
        $pageNo = isset($_REQUEST['pageNo']) ? (int)$_REQUEST['pageNo'] : 0;
        $limit = $this->ctx->getPageSize();
        
        $activityService = $this->oc->get(ActivityService::class);
        
        $r = $activityService->search($pageNo*$limit, $limit, $_REQUEST);
        
        $arr = array();
        $arr['listResponse'] = $r;
        
        
        $this->json($arr);
    }
    
}

