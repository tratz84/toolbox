<?php

namespace fail2ban\form;

class Fail2banSettingsForm extends \core\forms\CodegenBaseForm {

	public function __construct() {
		
		parent::__construct();
		
		$this->codegen();
		
	}
	
	
	
	public function codegen() {
		
		
		$w1 = new \core\forms\CheckboxField('fail2ban__enabled', NULL, t('Fail2ban enabled'));
		$this->addWidget( $w1 );
		$w2 = new \core\forms\FieldSetContainer('container-ip', t('IP threshold'));
		$this->addWidget( $w2 );
		
		$w3 = new \core\forms\NumberField('fail2ban__max_attempts_ip', NULL, t('Max. attempts'));
		$w2->addWidget( $w3 );
		$w3->setInfoText( t('Zero or negative to disable') );
		$w4 = new \core\forms\NumberField('fail2ban__max_attempts_ip_timespan', NULL, t('Time span'));
		$w2->addWidget( $w4 );
		$w4->setInfoText( t('In minutes') );
		$w5 = new \core\forms\FieldSetContainer('container-network', t('Network threshold'));
		$this->addWidget( $w5 );
		
		$w6 = new \core\forms\NumberField('fail2ban__max_attempts_network', NULL, t('Max. attempts'));
		$w5->addWidget( $w6 );
		$w6->setInfoText( t('Zero or negative to disable') );
		$w7 = new \core\forms\NumberField('fail2ban__max_attempts_network_timespan', NULL, t('Time span'));
		$w5->addWidget( $w7 );
		$w7->setInfoText( t('In minutes') );
		
	}










}

