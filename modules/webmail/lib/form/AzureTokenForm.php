<?php

namespace webmail\form;

use core\forms\validator\NotEmptyValidator;

class AzureTokenForm extends \core\forms\CodegenBaseForm {

	public function __construct() {
		
		parent::__construct();
		
		$this->codegen();
		
		
		$this->addValidator('description', new NotEmptyValidator());
		
	}
	
	
	
	public function codegen() {
		$this->addKeyField('webmail_azure_token_id');
		
		
		$w1 = new \core\forms\HiddenField('webmail_azure_token_id', NULL, t('Hidden field'));
		$this->addWidget( $w1 );
		$w2 = new \core\forms\TextField('description', NULL, t('Description'));
		$this->addWidget( $w2 );
		
	}


}

