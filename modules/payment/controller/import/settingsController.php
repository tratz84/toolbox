<?php



use core\controller\BaseController;
use payment\form\PaymentImportSettingsForm;
use base\service\SettingsService;

class settingsController extends BaseController {
    
    
    public function init() {
        $this->addTitle( t('Master data') );
        $this->addTitle( t('Payment import settings') );
    }
    
    
    public function action_index() {
        
        $this->form = new PaymentImportSettingsForm();
        
        if (is_get()) {
            $defaults = array();
            $defaults['import_payment_method_id'] = \core\Context::getInstance()->getSetting('payment_import_payment_method_id');
            $this->form->bind( $defaults );
        }
        
        if (is_post()) {
            $this->form->bind($_REQUEST);
            
            if ($this->form->validate()) {
                $settingsService = object_container_get(SettingsService::class);
                $settingsService->updateValue('payment_import_payment_method_id', $this->form->getWidgetValue('import_payment_method_id'));
                
                report_user_message('Changes saved');
                redirect('/?m=payment&c=import/settings');
            }
        }
        
        
        return $this->render();
    }
    
}


