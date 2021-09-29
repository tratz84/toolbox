<?php

namespace fail2ban\form;

use core\forms\validator\NotEmptyValidator;

class IpSettingForm extends \core\forms\CodegenBaseForm {

	public function __construct() {
		
		parent::__construct();
		
		$this->codegen();
		
		
		
		$this->addValidator('type', new NotEmptyValidator());
		$this->addValidator('ip', new NotEmptyValidator());
		
		
	}
	
	
	
	public function codegen() {
		$this->addKeyField('ip_setting_id');
		
		
		$w1 = new \core\forms\HiddenField('ip_setting_id', NULL, t('Hidden field'));
		$this->addWidget( $w1 );
		$w2 = new \core\forms\CheckboxField('active', NULL, t('Active'));
		$this->addWidget( $w2 );
		$w3 = new \core\forms\TextField('ip', NULL, t('IP'));
		$this->addWidget( $w3 );
		$w4 = new \core\forms\TextField('description', NULL, t('Description'));
		$this->addWidget( $w4 );
		$w5 = new \core\forms\RadioField('type', NULL, ['allow' => t('Allow'), 
		'block' => t('Block'), 
		], t('Type'));
		$this->addWidget( $w5 );
		$w6 = new \core\forms\TextareaField('note', NULL, t('Notitie'));
		$this->addWidget( $w6 );
		
	}






}

