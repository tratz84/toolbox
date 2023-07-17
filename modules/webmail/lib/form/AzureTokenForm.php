<?php

namespace webmail\form;

use core\forms\validator\NotEmptyValidator;
use webmail\service\CloudTokenService;

class AzureTokenForm extends \core\forms\CodegenBaseForm {

	public function __construct() {
		
		parent::__construct();
		
		$this->codegen();
		
		$ctService = object_container_get( CloudTokenService::class );
		$this->getWidget('return_url')->setValue( $ctService->getAzureRedirectUri() );
		
		$this->addValidator('description', new NotEmptyValidator());
		
	}
	
	
	
	public function codegen() {
		$this->addKeyField('webmail_azure_token_id');
		
		
		$w1 = new \core\forms\HiddenField('webmail_azure_token_id', NULL, t('Hidden field'));
		$this->addWidget( $w1 );
		$w2 = new \core\forms\HiddenField('request_token', NULL, t('Hidden field'));
		$this->addWidget( $w2 );
		$w3 = new \core\forms\FieldSetContainer('container-generic', t('Generic'));
		$this->addWidget( $w3 );
		
		$w4 = new \core\forms\TextField('description', NULL, t('Description'));
		$w3->addWidget( $w4 );
		$w5 = new \core\forms\FieldSetContainer('container-azure', t('Azure settings'));
		$this->addWidget( $w5 );
		
		$w6 = new \core\forms\TextField('azure_authorization_url', NULL, t('Authorization url'));
		$w5->addWidget( $w6 );
		$w7 = new \core\forms\TextField('azure_token_url', NULL, t('Token url'));
		$w5->addWidget( $w7 );
		$w8 = new \core\forms\TextField('azure_client_id', NULL, t('Client ID'));
		$w5->addWidget( $w8 );
		$w9 = new \core\forms\TextField('azure_client_secret', NULL, t('Client secret'));
		$w5->addWidget( $w9 );
		$w10 = new \core\forms\CopyToClipboardField('return_url', NULL, t('Return url'));
		$w5->addWidget( $w10 );
		$w10->setInfoText( t('Has to be set in Azure') );
		$w11 = new \core\forms\FieldSetContainer('container-status', t('Status'));
		$this->addWidget( $w11 );
		
		$w12 = new \core\forms\HtmlField('status', NULL, t('Status'));
		$w11->addWidget( $w12 );
		
	}











}

