<?php

namespace webmail\form;

class MailTabFilterListEdit extends \core\forms\ListEditWidget {

    protected static $getterName = 'objects';

	public function __construct() {
		parent::__construct( self::$getterName );
		
		$this->codegen();
	}
	
	
	
	public function codegen() {
		
		
		$w1 = new \core\forms\SelectField('filter_type', NULL, array (
		  'email' => 'Email',
		), 'E-mail');
		$this->addWidget( $w1 );
		$w2 = new \core\forms\TextField('filter_value', NULL, 'Filter string');
		$this->addWidget( $w2 );
		
	}



}

