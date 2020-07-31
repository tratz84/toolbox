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
		$w2 = new \core\forms\CheckboxField('enforce_when_no_mail', NULL, t('Enforce no email'));
		$this->addWidget( $w2 );
		$w2->setInfoText( t('If a user has no e-mailaddress configured, enforce two-fa-auth? Generates a page with an error that the user has no e-mailaddress') );
		$w3 = new \core\forms\RadioField('method', NULL, ['email' => t('E-mail'), 
		], t('Method'));
		$this->addWidget( $w3 );
		
	}




}

