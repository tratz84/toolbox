<?php

namespace codegen\form;

class ListFormGeneratorForm extends \core\forms\CodegenBaseForm {

	public function __construct() {
		
		parent::__construct();
		
		$this->codegen();
		
	}
	
	
	
	public function codegen() {
		$func1 = function() {  return array_merge(array('' => 'Make your choice'), codegen_map_form_classes());
		 }; 
		
		$w1 = new \core\forms\HiddenField('data', NULL, 'Hidden field');
		$this->addWidget( $w1 );
		$w2 = new \core\forms\TextField('name', NULL, 'Name');
		$this->addWidget( $w2 );
		$w3 = new \core\forms\TextField('short_description', NULL, 'Short description');
		$this->addWidget( $w3 );
		$w4 = new \core\forms\SelectField('form_class', NULL, $func1(), 'Form');
		$this->addWidget( $w4 );
		$w5 = new \core\forms\TextField('label', NULL, 'Label');
		$this->addWidget( $w5 );
		$w6 = new \core\forms\CheckboxField('sortable', NULL, 'Sortable');
		$this->addWidget( $w6 );
		
	}





}

