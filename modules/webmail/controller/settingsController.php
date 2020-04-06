<?php

use core\controller\BaseController;
use webmail\form\WebmailSettingsForm;
use base\service\SettingsService;
use webmail\WebmailSettings;

class settingsController extends BaseController {

	public function action_index() {
	
	    $this->form = new WebmailSettingsForm();
	    
	    if (is_post()) {
	        $this->form->bind( $_REQUEST );
	        
	        /** @var SettingsService $settingsService */
	        $settingsService = object_container_get(SettingsService::class);
	        $settingsService->updateValue('webmail_template_id_new_mail', $this->form->getWidgetValue('template_id_new_mail'));
	        $settingsService->updateValue('webmail_template_id_reply_mail', $this->form->getWidgetValue('template_id_reply_mail'));
	        $settingsService->updateValue('webmail_template_id_forward_mail', $this->form->getWidgetValue('template_id_forward_mail'));
	        
	        report_user_message(t('Changes saved'));
	        redirect('/?m=webmail&c=settings');
	    }
	    
	    /** @var WebmailSettings $webmailSettings */
	    $webmailSettings = object_container_get(WebmailSettings::class);
	    
	    $data = array();
	    $data['template_id_new_mail']     = $webmailSettings->getTemplateIdNewMail();
	    $data['template_id_reply_mail']   = $webmailSettings->getTemplateIdReplyMail();
	    $data['template_id_forward_mail'] = $webmailSettings->getTemplateIdForwardMail();
	    
	    $this->form->bind( $data );
	    

		$this->render();
	}


}

