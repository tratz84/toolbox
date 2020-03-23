<?php

namespace calendar\form;

class CalendarSettingsForm extends \core\forms\CodegenBaseForm {

	public function __construct() {
		
		parent::__construct();
		
		$this->codegen();
		
	}
	
	
	
	public function codegen() {
		
		
		$w1 = new \core\forms\CheckboxField('calendar_item_actions_enabled', NULL, t('Calendar Item actions'));
		$this->addWidget( $w1 );
		$w1->setInfoText( 'Adds field to calendar items & adds overview which uncompleted calendar-items' );
		
	}


}

