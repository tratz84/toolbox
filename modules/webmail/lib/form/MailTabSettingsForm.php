<?php

namespace webmail\form;

class MailTabSettingsForm extends \core\forms\CodegenBaseForm {

	public function __construct() {
		
		parent::__construct();
		
		$this->disableSubmit();
		
		$this->codegen();
		
	}
	
	
	
	public function codegen() {
		
		
		$w1 = new \core\forms\HiddenField('company_id', NULL, t('Hidden field'));
		$this->addWidget( $w1 );
		$w2 = new \core\forms\HiddenField('person_id', NULL, t('Hidden field'));
		$this->addWidget( $w2 );
		$w3 = new \core\forms\CheckboxField('default_filters', NULL, t('Default filters'));
		$this->addWidget( $w3 );
		$w3->setInfoText( 'Apply default filters? (e-mailaddresses set for customer)' );
		$w4 = new \webmail\form\MailTabFilterListEdit();
		$w4->setName( 'filters' );
		$w4->setMethodObjectList( 'filters' );
		$this->addWidget( $w4 );
		
	}



}

