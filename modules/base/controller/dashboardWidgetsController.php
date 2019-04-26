<?php



use core\controller\BaseController;
use base\service\ActivityService;

class dashboardWidgetsController extends BaseController {
    
    
    public function action_logActivity() {
        
        $activityService = $this->oc->get(ActivityService::class);
        
        $this->activities = $activityService->readForDashboard();
        
        
        $this->setShowDecorator(false);
        $this->render();
    }
}