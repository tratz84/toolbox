<?php



use core\controller\BaseController;
use report\service\ReportDashboardService;

class viewController extends BaseController {
    
    
    public function action_index() {
        
        $reportDashService = object_container_get( ReportDashboardService::class );
        $this->report = $reportDashService->readDashboard( get_var('id') );
        
        $this->forms = $this->report->getForms();
        foreach($this->forms as $f) {
            $f->bind( $_REQUEST );
        }
        
        
        $this->dwc = array();
        $this->dwc['saveEnabled'] = false;
        $this->dwc['fixed']       = true;
        $this->dwc['userWidgets'] = $this->report->getGridData();
        $this->dwc['widgets']     = array();
        
        
        
        $widgets = list_report_dashboard_widgets();
        $formClasses = array();
        foreach($widgets as $w) {
            $widgetCode = $w->getReportCode();
            
            $this->dwc['widgets'][] = array(
                'code'        => $widgetCode,
                'name'        => $w->getReportTitle(),
                'description' => $w->getReportDescription(),
                'ajaxUrl'     => $w->getAjaxUrl()
            );
            
            
            if ($w->getFormClass()) {
                $fc = $w->getFormClass();
                if (isset($formClasses[ $fc ]) == false) {
                    $formClasses[ $fc ] = array();
                    $formClasses[ $fc ][ 'widgetCodes' ] = array();
                    $formClasses[ $fc ][ 'description' ] = $fc;
                }
                
                $formClasses[ $fc ][ 'widgetCodes' ][] = $w->getReportCode();
            }
        }
        
        $this->addBodyClass( 'report-dashboard' );
        $this->addBodyClass( 'report-dashboard-widget-'.$this->report->getReportDashboardId() );
        
        
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

