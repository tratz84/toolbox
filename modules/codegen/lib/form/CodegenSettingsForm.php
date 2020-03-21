<?php

namespace codegen\form;

class CodegenSettingsForm extends \core\forms\CodegenBaseForm {

	public function __construct() {
		
		parent::__construct();
		
		$this->codegen();
		
	}
	
	
	
	public function codegen() {
		
		
		$w1 = new \core\forms\CheckboxField('codegen_autogenerate_dao', NULL, 'Autogen db model');
		$this->addWidget( $w1 );
		$w1->setInfoText( 'Autogenerate DBObject & DAO classes after tablemodel.php changed?' );
		
	}



}

