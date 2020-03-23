<?php

use core\controller\BaseController;
use calendar\form\CalendarSettingsForm;
use calendar\CalendarSettings;
use base\service\SettingsService;

class settingsController extends BaseController {

	public function action_index() {
	
	    /** @var CalendarSettings $calendarSettings */
	    $calendarSettings = object_container_get(CalendarSettings::class);
	    
	    $this->form = new CalendarSettingsForm();
	    
	    $settings = array();
	    $settings['calendar_item_actions_enabled'] = $calendarSettings->calendarItemActionsEnabled();
	    
	    $this->form->bind( $settings );
	    
	    
	    if (is_post()) {
	        $this->form->bind( $_REQUEST );
	        
	        $settings = $this->form->asArray();
	        
	        /** @var SettingsService $settingsService */
	        $settingsService = object_container_get(SettingsService::class);
	        $settingsService->updateValue('calendar_item_actions_enabled', $settings['calendar_item_actions_enabled'] ? '1' : '0');
	        
	        report_user_message(t('Changes saved'));
	        redirect('/?m=calendar&c=settings');
	    }
	    

		$this->render();

	}


}

