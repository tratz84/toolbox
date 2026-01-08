<?php

namespace securemail\form;

class SecureMessageSearchForm extends \core\forms\CodegenBaseForm {

	public function __construct() {
		
		parent::__construct();
		
		$this->codegen();
		
		
		$this->hideSubmitButtons();
	}
	
	
	
	public function codegen() {
		
		
		$w1 = new \core\forms\RadioField('showdel', NULL, [1 => t('Yes'), 
		0 => t('No'), 
		], t('Toon verwijderde'));
		$this->addWidget( $w1 );
		
	}


}

