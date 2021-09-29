<?php

namespace fail2ban\form;

class Fail2banSettingsForm extends \core\forms\CodegenBaseForm {

	public function __construct() {
		
		parent::__construct();
		
		$this->codegen();
		
	}
	
	
	
	public function codegen() {
		
		
		$w1 = new \core\forms\CheckboxField('fail2ban__enabled', NULL, t('Fail2ban enabled'));
		$this->addWidget( $w1 );
		
	}


}

