<?php

namespace report\form;

use core\forms\validator\NotEmptyValidator;

class ReportDashboardForm extends \core\forms\CodegenBaseForm {

	public function __construct() {
		
		parent::__construct();
		
		$this->codegen();
		
		$this->hideSubmitButtons();
		
		$this->addValidator('name', new NotEmptyValidator());
		
	}
	
	
	
	public function codegen() {
		
		
		$w1 = new \core\forms\HiddenField('report_dashboard_id', NULL, t('Hidden field'));
		$this->addWidget( $w1 );
		$w2 = new \core\forms\HiddenField('grid_data', NULL, t('Hidden field'));
		$this->addWidget( $w2 );
		$w3 = new \core\forms\CheckboxField('active', NULL, t('Active'));
		$this->addWidget( $w3 );
		$w4 = new \core\forms\TextField('name', NULL, t('Name'));
		$this->addWidget( $w4 );
		$w5 = new \core\forms\HtmlDatetimeField('edited', NULL, t('Edited'));
		$this->addWidget( $w5 );
		$w5->setOption( 'hide-when-invalid', true );
		$w6 = new \core\forms\HtmlDatetimeField('created', NULL, t('Created'));
		$this->addWidget( $w6 );
		$w6->setOption( 'hide-when-invalid', true );
		
	}





}

