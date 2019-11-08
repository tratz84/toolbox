<?php

namespace codegen\form;

class UserCapabilityListEdit extends \core\forms\ListEditWidget {

    protected static $getterName = 'capabilities';

	public function __construct() {
		parent::__construct( self::$getterName );
		
		$this->codegen();
	}
	
	
	
	public function codegen() {
		
		
		$w1 = new \core\forms\TextField('capability_code', NULL, 'Capability code');
		$this->addWidget( $w1 );
		$w2 = new \core\forms\TextField('short_description', NULL, 'Short description');
		$this->addWidget( $w2 );
		$w3 = new \core\forms\TextField('infotext', NULL, 'Info text popup');
		$this->addWidget( $w3 );
		
	}





}

