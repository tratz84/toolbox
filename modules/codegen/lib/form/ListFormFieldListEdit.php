<?php

namespace codegen\form;

class ListFormFieldListEdit extends \core\forms\ListEditWidget {

    protected static $getterName = 'fields';

	public function __construct() {
		parent::__construct( self::$getterName );
		
		$this->codegen();
	}
	
	
	
	public function codegen() {
		
		
		$w1 = new \core\forms\TextField('fieldname', NULL, 'Fieldname');
		$this->addWidget( $w1 );
		$w2 = new \core\forms\TextField('label', NULL, 'Label');
		$this->addWidget( $w2 );
		
	}


}

