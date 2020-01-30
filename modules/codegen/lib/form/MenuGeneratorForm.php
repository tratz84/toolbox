<?php

namespace codegen\form;

use core\forms\validator\NotEmptyValidator;

class MenuGeneratorForm extends \core\forms\CodegenBaseForm {

	public function __construct() {
		
		parent::__construct();
		
		hook_htmlscriptloader_enableGroup('jstree');
		
		$this->codegen();
		
		$this->addValidator('module_name', new NotEmptyValidator());
	}
	
	
	
	public function codegen() {
		$func1 = function() {  return codegen_map_modules();
		 }; 
		
		$w1 = new \core\forms\HiddenField('treedata', NULL, 'Hidden field');
		$this->addWidget( $w1 );
		$w2 = new \core\forms\SelectField('module_name', NULL, $func1(), 'Module');
		$this->addWidget( $w2 );
		
	}



}

