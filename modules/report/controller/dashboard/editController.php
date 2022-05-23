<?php




use core\controller\BaseController;

class editController extends BaseController {
    
    
    public function action_index() {
        $widgets = list_report_dashboard_widgets();
        
        $this->dwc = array();
        $this->dwc['saveUrl'] = appUrl('/?m=report&c=dashboard/edit&a=save');
        
        $this->dwc['userWidgets'] = array();
        
        $this->dwc['widgets'] = array();
        foreach($widgets as $w) {
            $this->dwc['widgets'][] = array(
                'widgetCode' => $w->getReportCode(),
                'name' => $w->getReportTitle(),
                'description' => $w->getReportDescription()
            );
        }
        
        return $this->render();
    }
    
    
    public function action_save() {
        
    }
    
    
    
}


