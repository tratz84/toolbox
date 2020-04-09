<?php

namespace webmail\form;

class PurgeJunkForm extends \core\forms\CodegenBaseForm {

	public function __construct() {
		
		parent::__construct();
		
		$this->codegen();
		
		$this->disableSubmit();
		$this->hideSubmitButtons();
	}
	
	
	
	public function codegen() {
		$func1 = function() {  return mapAllConnectors(); }; 
		
		$w1 = new \core\forms\SelectField('connectorId', NULL, $func1(), t('Connector'));
		$this->addWidget( $w1 );
		
	}



}

