<?php


use core\controller\BaseController;
use codegen\form\CodegenSettingsForm;
use base\service\SettingsService;

class settingsController extends BaseController {
    
    
    public function action_index() {
        
        $this->form = new CodegenSettingsForm();
        
        $props = array();
        $props['codegen_autogenerate_dao'] = $this->ctx->getSetting('codegen_autogenerate_dao');
        $this->form->bind( $props );
        
        if (is_post()) {
            $this->form->bind( $_REQUEST );
            if ($this->form->validate()) {
                $settingsService = object_container_get(SettingsService::class);
                $settingsService->updateValue('codegen_autogenerate_dao', $this->form->getWidgetValue('codegen_autogenerate_dao') ? '1' : '0');
                
                report_user_message('Settings saved');
                redirect('/?m=codegen&c=menu');
            }
            
        }
        
        
        return $this->render();
    }
    
    
}
