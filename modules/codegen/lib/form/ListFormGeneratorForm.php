<?php

namespace codegen\form;

use core\forms\validator\NotEmptyValidator;

class ListFormGeneratorForm extends \core\forms\CodegenBaseForm {

	public function __construct() {
		
		parent::__construct();
		
		$this->codegen();
		
		
		$this->addValidator('module_name', new NotEmptyValidator());
		$this->addValidator('name', new NotEmptyValidator());
		
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
		$func2 = function() {  return array_merge(array('' => 'Make your choice'), codegen_map_form_classes());
		 }; 
		
		$w1 = new \core\forms\HiddenField('data', NULL, 'Hidden field');
		$this->addWidget( $w1 );
		$w2 = new \core\forms\SelectField('module_name', NULL, $func1(), 'Module');
		$this->addWidget( $w2 );
		$w3 = new \core\forms\TextField('name', NULL, 'Name');
		$this->addWidget( $w3 );
		$w4 = new \core\forms\TextField('short_description', NULL, 'Short description');
		$this->addWidget( $w4 );
		$w5 = new \core\forms\SelectField('form_class', NULL, $func2(), 'Form');
		$this->addWidget( $w5 );
		$w6 = new \core\forms\TextField('label', NULL, 'Label');
		$this->addWidget( $w6 );
		$w6->setInfoText( 'Label shown in form where widget is used' );
		$w7 = new \core\forms\CheckboxField('sortable', NULL, 'Sortable');
		$this->addWidget( $w7 );
		$w8 = new \codegen\form\ListFormWidgetFieldList();
		$this->addWidget( $w8 );
		$w9 = new \codegen\form\ListFormWidgetPublicFieldList();
		$this->addWidget( $w9 );
		
	}

















}

