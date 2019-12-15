<?php



use core\controller\BaseController;
use core\event\EventBus;
use report\ReportMenuList;

class reportController extends BaseController {
    
    protected $reportNotFound = false;
    protected $showIndex = false;
    
    
    public function init() {
        checkCapability('report', 'show-reports');
    }
    
    
    public function action_index() {
        
        $this->rml = new ReportMenuList();
        $this->divReportClasses = array();
        
        $eb = $this->oc->get(EventBus::class);
        $eb->publishEvent($this->rml, 'report', 'menu-list');

        // get current reportClass
        $this->report = null;
        if (get_var('controllerName')) {
            
            list ($module, $controller) = explode('@', get_var('controllerName'), 2);
            
            foreach($this->rml->getMenuItems() as $r) {
                if ($r->getModule() == $module && $r->getControllerName() && $controller == $r->getControllerName()) {
                    $this->report = $r;
                    break;
                }
            }
            
            if (!$this->report) {
                $this->reportNotFound = true;
            }
        }
        
        if ($this->report) {
            $ctrl = $this->oc->getController($this->report->getModule(), $this->report->getControllerName());
            
            $this->divReportClasses[] = 'module-' . slugify($this->report->getModule());
            $this->divReportClasses[] = 'controller-' . slugify($this->report->getControllerName());
            
            $this->reportHtml = $ctrl->report();
        }
        
        // no report selected? => show index
        if ($this->reportNotFound == false && !$this->report) {
            $this->showIndex = true;
        }
        
        
        $this->render();
    }
}
