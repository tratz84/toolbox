<?php

use core\controller\BaseController;
use twofaauth\form\TwoFactorSettingsForm;
use twofaauth\TwoFaAuthSettings;
use base\service\SettingsService;

class settingsController extends BaseController {

	public function action_index() {
	

	    $faAuthSettings = object_container_get( TwoFaAuthSettings::class );
	    
	    $this->form = object_container_create( TwoFactorSettingsForm::class );
	    
	    $arr = array();
	    $arr['enabled'] = $faAuthSettings->getEnabled() ? '1' : '0';
	    $arr['method']  = $faAuthSettings->getAuthMethod();
	    
	    $this->form->bind( $arr );
	    
	    
	    if (is_post()) {
	        $settingsService = object_container_get( SettingsService::class );
	        $settingsService->updateValue('twofaauth__enabled', get_var('enabled') ? '1' : '0');
	        $settingsService->updateValue('twofaauth__auth_method', get_var('auth_method'));
	        
	        report_user_message(t('Changes saved'));
	        redirect('/?m=twofaauth&c=settings');
	    }
	    
	    
	    
		$this->render();

	}

}

