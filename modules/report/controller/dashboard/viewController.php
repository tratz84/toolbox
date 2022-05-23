<?php



use core\controller\BaseController;

class viewController extends BaseController {
    
    
    
    
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

