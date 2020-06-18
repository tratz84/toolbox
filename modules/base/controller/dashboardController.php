<?php



use base\DashboardWidgets;
use base\service\MetaService;
use core\controller\BaseController;

class dashboardController extends BaseController {
    
    
    public function action_index() {
        $this->addTitle('Dashboard');
        
        $this->dwc = new DashboardWidgets();
        
        /**
         * @var \event\EventBus $eb
         */
        $eb = $this->oc->get(\core\event\EventBus::class);
        $eb->publishEvent($this->dwc, 'base', 'dashboard');
        
        $userId = $this->ctx->getUser()->getUserId();
        $metaService = $this->oc->get(MetaService::class);
        $userWidgets = @unserialize( $metaService->getMetaValue('user', $userId, 'dashboard_widgets') );
        
        if ($userWidgets) foreach($userWidgets as $key => $arr) {
            $this->dwc->addUserWidget($key, $arr['x'], $arr['y'], $arr['width'], $arr['height']);
        }
        
        // sort by name
        usort($this->dwc->widgets, function($w1, $w2) {
            return strcmp($w1['name'], $w2['name']);
        });
        
        
        $this->render();
    }
    
    public function action_save() {
        $widgets = explode(',', $_REQUEST['enabledWidgets']);
        
        $userWidgets = array();
        
        foreach($widgets as $w) {
            $w = trim($w);
            if (!$w) continue;
            
            $userWidgets[$w] = $_REQUEST[$w];
        }
        
        $userId = $this->ctx->getUser()->getUserId();
        $metaService = $this->oc->get(MetaService::class);
        $metaService->saveMeta('user', $userId, 'dashboard_widgets', serialize($userWidgets));
        
        print 'OK';
    }
    
}

