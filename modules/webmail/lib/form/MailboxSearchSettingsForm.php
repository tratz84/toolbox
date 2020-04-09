<?php

namespace webmail\form;

class MailboxSearchSettingsForm extends \core\forms\CodegenBaseForm {

	public function __construct() {
		
		parent::__construct();
		
		$this->disableSubmit();
		
		$this->codegen();
		
	}
	
	
	
	public function codegen() {
		
		
		$w1 = new \core\forms\HtmlField('lblIncludes', NULL, t('Includes'));
		$this->addWidget( $w1 );
		$w2 = new \webmail\form\MailTabFilterListEdit();
		$w2->setName( 'includeFilters' );
		$w2->setMethodObjectList( 'includeFilters' );
		$this->addWidget( $w2 );
		$w3 = new \core\forms\HtmlField('lblExcludes', NULL, t('Excludes'));
		$this->addWidget( $w3 );
		$w4 = new \webmail\form\MailTabFilterListEdit();
		$w4->setName( 'excludeFilters' );
		$w4->setMethodObjectList( 'excludeFilters' );
		$this->addWidget( $w4 );
		$w5 = new \core\forms\HtmlField('lblHide', NULL, t('Hide folders'));
		$this->addWidget( $w5 );
		$w6 = new \webmail\form\MailboxHideFolderListEdit();
		$w6->setName( 'hideFolderList' );
		$w6->setMethodObjectList( 'hideFolderList' );
		$this->addWidget( $w6 );
		
	}




}

