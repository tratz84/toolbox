<?php




use core\controller\BaseController;
use report\form\ReportDashboardForm;
use report\service\ReportDashboardService;
use report\model\ReportDashboard;

class editController extends BaseController {
    
    
    public function action_index() {
        
        $this->form = new ReportDashboardForm();
        $reportDashService = object_container_get( ReportDashboardService::class );
        
        // bind
        if (get_var('id')) {
            $reportDashboard = $reportDashService->readDashboard( get_var('id') );
        }
        else {
            $reportDashboard = new ReportDashboard();
        }
        $this->form->bind( $reportDashboard );
        
        // save
        if (is_post()) {
            $this->form->bind( $_REQUEST );
            if ($this->form->validate()) {
                $reportDashboard = $reportDashService->saveDashboard( $this->form );
                
                report_user_message( t('Changes saved') );
                
                redirect('/?m=report&c=dashboard/edit&id=' . $reportDashboard->getReportDashboardId());
            }
        }
        
        
        
        
        $widgets = list_report_dashboard_widgets();
        
        $this->dwc = array();
        $this->dwc['saveUrl'] = appUrl('/?m=report&c=dashboard/edit&a=save');
        $this->dwc['userWidgets'] = array();
        $this->dwc['widgets'] = array();
        foreach($widgets as $w) {
            $widgetCode = $w->getReportCode();
            
            $this->dwc['widgets'][] = array(
                'code'        => $widgetCode,
                'name'        => $w->getReportTitle(),
                'description' => $w->getReportDescription(),
                'ajaxUrl'     => $w->getAjaxUrl()
            );
        }
        
        return $this->render();
    }
    
    
    public function action_save() {
        
    }
    
    
    
}


