<?php

namespace codegen\form;



use core\forms\validator\NotEmptyValidator;

class ListEditGeneratorForm extends \core\forms\CodegenBaseForm {

	public function __construct() {
		
		parent::__construct();
		
		$this->codegen();
		
		
		$this->addValidator('module_name', new NotEmptyValidator());
		$this->addValidator('name', new NotEmptyValidator());
		$this->addValidator('name', function($form) {
		    $n = $form->getWidgetValue('name');
		    
		    if (endsWith($n, 'ListEdit') == false) {
		        return 'name must end with ListEdit';
		    }
		});
	}
	
	
	
	public function codegen() {
		$func1 = function() {  
		
		return codegen_map_modules();
		 }; 
		
		$w1 = new \core\forms\HiddenField('data', NULL, '');
		$this->addWidget( $w1 );
		$w2 = new \core\forms\SelectField('module_name', NULL, $func1(), 'Module');
		$this->addWidget( $w2 );
		$w3 = new \core\forms\TextField('name', NULL, 'Name');
		$this->addWidget( $w3 );
		$w4 = new \core\forms\TextField('short_description', NULL, 'Short description');
		$this->addWidget( $w4 );
		$w5 = new \core\forms\TextField('objects_getter', NULL, 'Objects getter-name');
		$this->addWidget( $w5 );
		
	}











}

