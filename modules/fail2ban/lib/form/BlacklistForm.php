<?php

namespace fail2ban\form;

class BlacklistForm extends \core\forms\CodegenBaseForm {

	public function __construct() {
		
		parent::__construct();
		
		$this->codegen();
		
	}
	
	
	
	public function codegen() {
		$this->addKeyField('blacklist_id');
		
		
		$w1 = new \core\forms\HiddenField('blacklist_id', NULL, t('Hidden field'));
		$this->addWidget( $w1 );
		$w2 = new \core\forms\TextField('networkAddress', NULL, t('Network address'));
		$this->addWidget( $w2 );
		$w3 = new \core\forms\TextField('Note', NULL, t('note'));
		$this->addWidget( $w3 );
		$w4 = new \core\forms\CheckboxField('active', NULL, t('Active'));
		$this->addWidget( $w4 );
		$w5 = new \core\forms\HtmlDatetimeField('created', NULL, t('Created'));
		$this->addWidget( $w5 );
		
	}



	public static function codegenDbMapper() {
		$fdm = new \core\service\FormDbMapper( self::class, \fail2ban\model\BlacklistDAO::class );
		return $fdm;
	}


	public static function getDbMapper() {
		$m = self::codegenDbMapper();
		return $m;
	}

}

