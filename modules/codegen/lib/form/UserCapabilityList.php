<?php
namespace codegen\form;



class UserCapabilityList extends \core\forms\ListEditWidget {

	public function __construct() {
		
		parent::__construct('objects');
		
		$this->codegen();
		
	}
	
	
	
	public function codegen() {
		
		
		$w1 = new \core\forms\TextField('capability_code', NULL, 'Capability code');
		$this->addWidget( $w1 );
		$w2 = new \core\forms\TextField('short_description', NULL, 'Korte omschrijving');
		$this->addWidget( $w2 );
		$w3 = new \core\forms\TextField('infotext', NULL, 'Info text popup');
		$this->addWidget( $w3 );
		
	}







}

