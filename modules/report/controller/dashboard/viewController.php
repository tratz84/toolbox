<?php



use core\controller\BaseController;
use report\service\ReportDashboardService;

class viewController extends BaseController {
    
    
    
    
    public function action_index() {
        
        $reportDashService = object_container_get( ReportDashboardService::class );
        $this->report = $reportDashService->readDashboard( get_var('id') );
        
        
        $formClasses = $this->report->getFormClasses();
        $this->forms = array();
        foreach($formClasses as $fc) {
            $f = new $fc();
            
            $this->forms[$fc] = $f;
        }
        
        
        
        return $this->render();
    }
    
    
    
    
    public function action_render_widget() {
        $widgets = list_report_dashboard_widgets();
        
        $code = get_var('code');
        
        $this->widget = null;
        foreach($widgets as $w) {
            if ($w->getReportCode() == $code) {
                $this->widget = $w;
                break;
            }
        }
        
        $this->setShowDecorator(false);
        
        return $this->render();
    }
    
}

