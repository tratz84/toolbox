<?php

namespace codegen\form;



class AbcTestForm extends CodegenBaseForm {

	public function __construct() {
		
		parent::__construct();
		
		$this->codegen();
		
	}
	
	
	
	public function codegen() {
		
		
		$w1 = new core\forms\WidgetContainer('container');
		$this->addWidget( $w1 );
		
		$w2 = new core\forms\TextField('voornaam', NULL, 'Firstname');
		$w1->addWidget( $w2 );
		$w3 = new core\forms\CheckboxField('agree', NULL, 'Akkoord?');
		$w1->addWidget( $w3 );
		$w4 = new core\forms\ColorPickerField(NULL, NULL, 'Color picker');
		$w1->addWidget( $w4 );
		
	}


}

