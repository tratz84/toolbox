<?php

namespace twofaauth\form;

class TwoFactorSettingsForm extends \core\forms\CodegenBaseForm {

	public function __construct() {
		
		parent::__construct();
		
		$this->codegen();
		
	}
	
	
	
	public function codegen() {
		
		
		$w1 = new \core\forms\CheckboxField('enabled', NULL, t('Enabled'));
		$this->addWidget( $w1 );
		$w2 = new \core\forms\RadioField('method', NULL, ['email' => t('E-mail'), 
		], t('Method'));
		$this->addWidget( $w2 );
		
	}



}

