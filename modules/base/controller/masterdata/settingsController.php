<?php



use base\service\SettingsService;
use core\controller\BaseController;

class settingsController extends BaseController {
    
    public function init() {
        checkCapability('base', 'edit-masterdata');
    }
    
    public function action_index() {
        
        $settingsService = $this->oc->get(SettingsService::class);
        $this->settings = $settingsService->settingsAsMap();
        
        $this->availableModules = $settingsService->getModuleList();
        
        usort($this->availableModules, function($m1, $m2) {
            // sort by prio
            $p = ($m1->getPrio() - $m2->getPrio());
            if ($p != 0) {
                return ceil( $p );
            }
            
            // sort by name
            return strcmp($m1->getName(), $m2->getName()) * -1;
        });
        
        if (is_post()) {
            
            $pageSize = (int)$_REQUEST['PAGE_SIZE'];
            if ($pageSize >= 10 && $_REQUEST['PAGE_SIZE'] < 100)
                $settingsService->updateValue('PAGE_SIZE', $pageSize);

            foreach($this->availableModules as $m) {
                $tag = $m->getTag();
                
                if (get_var($tag.'Enabled')) {
                    $settingsService->updateValue($tag.'Enabled', 1);
                } else {
                    $settingsService->updateValue($tag.'Enabled', 0);
                }
            }
            
            $settingsService->updateValue('master_base_color', get_var('master_base_color'));
                
            redirect('/?m=base&c=masterdata/index');
        }
        
        $this->render();
    }
    
}