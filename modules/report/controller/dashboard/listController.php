<?php

use core\controller\BaseController;
use report\service\ReportDashboardService;

class listController extends BaseController {
    
    public function action_index() {
        
        $rdService = object_container_get( ReportDashboardService::class );
        
        $this->reportDashboards = $rdService->readDashboards();
        
        
        return $this->render();
    }
    
    
}

