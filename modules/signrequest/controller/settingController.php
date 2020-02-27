<?php



use base\service\SettingsService;
use core\controller\BaseController;

class settingController extends BaseController {
    
    public function init() {
        $this->addTitle('SignRequest settings');
    }
    
    
    public function action_index() {
        
        $settingsService = $this->oc->get(SettingsService::class);
        $this->settings = $settingsService->settingsAsMap();
        
        if (is_post()) {
            $settingsService->updateValue('signrequestToken', get_var('signrequestToken'));
            
            redirect('/?m=base&c=masterdata/index');
        }
        
        $this->render();
    }
    
    
}
