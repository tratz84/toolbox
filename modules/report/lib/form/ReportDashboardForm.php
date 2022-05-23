<?php

namespace report\form;

class ReportDashboardForm extends \core\forms\CodegenBaseForm {

	public function __construct() {
		
		parent::__construct();
		
		$this->codegen();
		
		$this->hideSubmitButtons();
	}
	
	
	
	public function codegen() {
		
		
		$w1 = new \core\forms\CheckboxField('active', NULL, t('Active'));
		$this->addWidget( $w1 );
		$w2 = new \core\forms\TextField('description', NULL, t('Description'));
		$this->addWidget( $w2 );
		$w3 = new \core\forms\HtmlDatetimeField('edited', NULL, t('Edited'));
		$this->addWidget( $w3 );
		$w3->setOption( 'hide-when-invalid', true );
		$w4 = new \core\forms\HtmlDatetimeField('created', NULL, t('Created'));
		$this->addWidget( $w4 );
		$w4->setOption( 'hide-when-invalid', true );
		
	}



}

