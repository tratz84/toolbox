<?php

namespace base\form;

class CronSettingsForm extends \core\forms\CodegenBaseForm {

	public function __construct() {
		
		parent::__construct();
		
		$this->codegen();
		
	}
	
	
	
	public function codegen() {
		
		
		$w1 = new \core\forms\NumberField('cron_daily_start_hour', NULL, t('Start hour'));
		$this->addWidget( $w1 );
		$w1->setInfoText( t('Hour daily-cron is started') );
		
	}



}

