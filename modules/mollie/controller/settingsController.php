<?php



use core\controller\BaseController;
use mollie\form\MollieSettingsForm;
use base\service\SettingsService;

class settingsController extends BaseController {
    
    
    
    public function action_index() {
        
        $this->form = object_container_create(MollieSettingsForm::class);
        
        
        if (is_post()) {
            $this->form->bind($_REQUEST);
            
            $sservice = object_container_get( SettingsService::class );
            
            $sservice->updateValue('mollie_api_key', $this->form->getWidgetValue('mollie_api_key'));
            
            report_user_message( t('Changes saved') );
            redirect( '/?m=mollie&c=settings' );
        }
        
        $arr = array();
        $arr['mollie_api_key'] = ctx()->getSetting('mollie_api_key');
        $this->form->bind( $arr );
        
        
        return $this->render();
    }
    
    
}

