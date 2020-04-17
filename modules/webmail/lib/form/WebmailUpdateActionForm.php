<?php

namespace webmail\form;

class WebmailUpdateActionForm extends \core\forms\CodegenBaseForm {

	public function __construct() {
		
		parent::__construct();
		
		$this->codegen();
		
		$this->disableSubmit();
		$this->hideSubmitButtons();
		
	}
	
	
	
	public function codegen() {
		$func1 = function() {  return mapMailActions(); }; 
		$func2 = function() {  return mapMailActions(); }; 
		
		$w1 = new \core\forms\SelectField('old_action', NULL, $func1(), t('Old action'));
		$this->addWidget( $w1 );
		$w2 = new \core\forms\SelectField('new_action', NULL, $func2(), t('New action'));
		$this->addWidget( $w2 );
		
	}



}

