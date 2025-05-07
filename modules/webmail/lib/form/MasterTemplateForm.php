<?php

namespace webmail\form;

use core\forms\validator\NotEmptyValidator;

class MasterTemplateForm extends \core\forms\CodegenBaseForm {

	public function __construct() {
		
		parent::__construct();
		
		$this->codegen();
		
		$this->enctypeToMultipartFormdata();
		
		$this->addValidator('name', new NotEmptyValidator());
		
		
	}
	
	
	
	public function codegen() {
		$this->addKeyField('master_template_id');
		
		
		$w1 = new \core\forms\HiddenField('master_template_id', NULL, t('Hidden field'));
		$this->addWidget( $w1 );
		$w2 = new \core\forms\TextField('name', NULL, t('Name'));
		$this->addWidget( $w2 );
		$w3 = new \core\forms\FileField('f', NULL, t('File'));
		$this->addWidget( $w3 );
		$w4 = new \core\forms\HtmlDatetimeField('edited', NULL, t('Last modified'));
		$this->addWidget( $w4 );
		$w4->setOption( 'hide-when-invalid', true );
		$w5 = new \core\forms\HtmlDatetimeField('created', NULL, t('Created'));
		$this->addWidget( $w5 );
		$w5->setOption( 'hide-when-invalid', true );
		
	}




}

