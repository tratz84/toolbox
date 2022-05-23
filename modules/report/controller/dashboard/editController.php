<?php




use core\controller\BaseController;
use report\form\ReportDashboardForm;
use report\service\ReportDashboardService;
use report\model\ReportDashboard;

class editController extends BaseController {
    
    public function init() {
        
        checkCapability( 'report', 'edit-dashboard' );
        
    }
    
    
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
        $this->dwc['saveEnabled'] = false;
        if ($reportDashboard->getGridData())
            $this->dwc['userWidgets'] = $reportDashboard->getGridData();
        else
            $this->dwc['userWidgets'] = array();
        $this->dwc['widgets']     = array();
        
        
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
        
        // sort form-classes
        $fcs = $reportDashboard->getFormClasses();
        $fcKeys = array_keys( $formClasses );
        usort($fcKeys, function($o1, $o2) use ($fcs) {
            $p1 = pos_in_array($o1, $fcs);
            $p2 = pos_in_array($o2, $fcs);
            
            return $p1 - $p2;
        });
        
        $this->formClasses = array();
        foreach($fcKeys as $fck) {
            $this->formClasses[$fck] = $formClasses[$fck];
        }
        
        
        if (is_post()) {
            $this->dwc['userWidgets'] = json_decode( $_POST['grid_data'] );
        }
        
        
        return $this->render();
    }
    
    
    public function action_save() {
        
    }
    
    
    
}


