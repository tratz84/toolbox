<?php

namespace base\form;

class SelectPersonListEdit extends \core\forms\ListEditWidget {

    protected static $getterName = 'personList';

	public function __construct() {
		parent::__construct( self::$getterName );
		
        hook_htmlscriptloader_enableGroup('select-person-list-edit');
		
		$this->codegen();
		
	}
	
	
	
	public function codegen() {
		
		$this->setShowNoResultsMessage( true );
		
		
		$w1 = new \core\forms\HiddenField('company_person_id', NULL, t('Hidden field'));
		$this->addWidget( $w1 );
		$w2 = new \core\forms\HiddenField('person_id', NULL, t('Hidden field'));
		$this->addWidget( $w2 );
		$w3 = new \core\forms\HtmlField('fullName', NULL, t('Fullname'));
		$this->addWidget( $w3 );
		
	}













}

