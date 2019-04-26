<?php



use core\controller\BaseController;
use base\service\ActivityService;

class activityReportController extends BaseController {
    
    
    public function report($render=true) {
        
        
        
        
        if ($render)
            return $this->renderToString();
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
    
    
    
    public function action_popup() {
        
        $activityService = $this->oc->get(ActivityService::class);
        
        $this->activity = $activityService->readActivity($_REQUEST['id']);
        
        $this->changes = @unserialize($this->activity->getChanges());
        if ($this->changes === false)
            $this->changes = $this->activity->getChanges();
        
        $this->setShowDecorator(false);
        $this->render();
    }
    
    
}