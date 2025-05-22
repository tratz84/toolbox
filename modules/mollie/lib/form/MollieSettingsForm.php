<?php

namespace mollie\form;

class MollieSettingsForm extends \core\forms\CodegenBaseForm {

	public function __construct() {
		
		parent::__construct();
		
		$this->codegen();
		
		$this->hideSubmitButtons();
	}
	
	
	
	public function codegen() {
		
		
		$w1 = new \core\forms\TextField('mollie_api_key', NULL, t('API Key'));
		$this->addWidget( $w1 );
		
	}


}

