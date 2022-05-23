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
		
		
		$w1 = new \core\forms\WidgetContainer('container-base');
		$this->addWidget( $w1 );
		
		$w2 = new \core\forms\WidgetContainer('container-left');
		$w1->addWidget( $w2 );
		
		$w3 = new \core\forms\HiddenField('report_dashboard_id', NULL, t('Hidden field'));
		$w2->addWidget( $w3 );
		$w4 = new \core\forms\HiddenField('settings', NULL, t('Hidden field'));
		$w2->addWidget( $w4 );
		$w5 = new \core\forms\CheckboxField('active', NULL, t('Active'));
		$w2->addWidget( $w5 );
		$w6 = new \core\forms\TextField('name', NULL, t('Name'));
		$w2->addWidget( $w6 );
		$w7 = new \core\forms\HtmlDatetimeField('edited', NULL, t('Edited'));
		$w2->addWidget( $w7 );
		$w7->setOption( 'hide-when-invalid', true );
		$w8 = new \core\forms\HtmlDatetimeField('created', NULL, t('Created'));
		$w2->addWidget( $w8 );
		$w8->setOption( 'hide-when-invalid', true );
		$w9 = new \core\forms\WidgetContainer('container-right');
		$w1->addWidget( $w9 );
		
		$w10 = new \core\forms\HtmlField('container-forms', NULL, t('Forms'));
		$w9->addWidget( $w10 );
		
	}









}

