<?php

use core\controller\BaseController;
use base\service\SettingsService;

class settingsController extends BaseController {

	public function action_index() {
	
	    
	    $this->form = new \fail2ban\form\Fail2banSettingsForm();
	    
	    // bind to current settings
	    $arr = array();
	    $arr['fail2ban__enabled'] = fail2ban_enabled() ? 1 : 0;
	    $this->form->bind( $arr );
	    
	    
	    if (is_post()) {
	        $this->form->bind( $_REQUEST );
	        
            $settingsService = object_container_get( SettingsService::class );
	        
            // update
	        $f2b_enabled = $this->form->getWidgetValue('fail2ban__enabled') ? 1 : 0;
	        $settingsService->updateValue( 'fail2ban__enabled', $f2b_enabled );
	        
	        // message & redir
	        report_user_message( t('Changes saved') );
	        redirect( '/?m=fail2ban&c=settings' );
	    }
	    

		$this->render();

	}


}

