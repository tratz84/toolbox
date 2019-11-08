<?php

namespace codegen\form;

class ListFormWidgetPublicFieldList extends \core\forms\ListEditWidget {
    
    protected static $getterName = 'publicFields';

	public function __construct() {
		parent::__construct( self::$getterName );
		
		$this->codegen();
	}
	
	
	
	public function codegen() {
		
		
		$w1 = new \core\forms\TextField('publicfieldname', NULL, 'Fieldname');
		$this->addWidget( $w1 );
		
	}





}

