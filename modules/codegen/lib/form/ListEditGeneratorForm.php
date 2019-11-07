<?php

namespace codegen\form;



class ListEditGeneratorForm extends \core\forms\CodegenBaseForm {

	public function __construct() {
		
		parent::__construct();
		
		$this->codegen();
		
	}
	
	
	
	public function codegen() {
		
		
		$w1 = new \core\forms\TextField('name', NULL, 'Name');
		$this->addWidget( $w1 );
		
	}


}

