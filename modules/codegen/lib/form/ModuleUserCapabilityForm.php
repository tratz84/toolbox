<?php

namespace codegen\form;

use core\forms\validator\NotEmptyValidator;

class ModuleUserCapabilityForm extends \core\forms\CodegenBaseForm {

	public function __construct() {
		
		parent::__construct();
		
		$this->codegen();
		
		$this->addValidator('module_name', new NotEmptyValidator());
	}
	
	
	
	public function codegen() {
		$func1 = function() {  return codegen_map_modules(); }; 
		
		$w1 = new \core\forms\SelectField('module_name', NULL, $func1(), t('Module'));
		$this->addWidget( $w1 );
		$w2 = new \codegen\form\UserCapabilityListEdit();
		$w2->setName( 'capabilities' );
		$w2->setMethodObjectList( 'capabilities' );
		$this->addWidget( $w2 );
		
	}





}

