<?php

namespace base\form;

class SelectCompanyListEdit extends \core\forms\ListEditWidget {

    protected static $getterName = 'companyList';

	public function __construct() {
		parent::__construct( self::$getterName );
		
		$this->codegen();
		
		$this->setShowNoResultsMessage(true);
	}
	
	
	
	public function codegen() {
		
		
		$w1 = new \core\forms\HiddenField('company_person_id', NULL, t('Hidden field'));
		$this->addWidget( $w1 );
		$w2 = new \core\forms\HiddenField('company_id', NULL, t('Hidden field'));
		$this->addWidget( $w2 );
		$w3 = new \core\forms\HtmlField('company_name', NULL, t('Company name'));
		$this->addWidget( $w3 );
		
	}



}

