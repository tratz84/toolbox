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
		$func1 = function() {  
		
		$map = array();
		$map[''] = 'Make your choice';
		foreach(module_list() as $m => $p) {
		$map[$m] = $m;
		}
		
		return $map;
		 }; 
		
		$w1 = new \core\forms\SelectField('module_name', NULL, $func1(), 'Module');
		$this->addWidget( $w1 );
		$w2 = new \codegen\form\UserCapabilityListEdit();
		$this->addWidget( $w2 );
		
	}



}

