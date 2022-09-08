<?php

namespace codegen\form;

class MenuGeneratorItemForm extends \core\forms\CodegenBaseForm {

	public function __construct() {
		
		parent::__construct();
		
		$this->disableSubmit();
		$this->hideSubmitButtons();
		
		$this->codegen();
		
	}
	
	
	
	public function codegen() {
		
		
		$w1 = new \core\forms\SelectField('icon', NULL, ['fa-tags' => t('fa-tags'), 
		'fa-tasks' => t('fa-tasks'), 
		'fa-calendar' => t('fa-calendar'), 
		'fa-sign-out' => t('fa-sign-out'), 
		'fa-bug' => t('fa-bug'), 
		'fa-bars' => t('fa-bars'), 
		'fa-file-archive-o' => t('fa-file-archive-o'), 
		'fa-dashboard' => t('fa-dashboard'), 
		'fa-user' => t('fa-user'), 
		'fa-share-alt' => t('fa-share-alt'), 
		'fa-money' => t('fa-money'), 
		'fa-signal' => t('fa-signal'), 
		'fa-wrench' => t('fa-wrench'), 
		'fa-file' => t('fa-file'), 
		'fa-clipboard-check' => t('fa-clipboard-check'), 
		], t('Icon'));
		$this->addWidget( $w1 );
		$w2 = new \core\forms\TextField('label', NULL, t('Label'));
		$this->addWidget( $w2 );
		$w3 = new \core\forms\TextField('url', NULL, t('Url'));
		$this->addWidget( $w3 );
		$w4 = new \core\forms\NumberField('weight', NULL, t('Weight'));
		$this->addWidget( $w4 );
		$w5 = new \core\forms\CheckboxField('as_first_child', NULL, t('As first child'));
		$this->addWidget( $w5 );
		$w5->setInfoText( t('In case of a submenu, this menu-item on top?') );
		$w6 = new \core\forms\TextareaField('menuPermission', NULL, t('Permission'));
		$this->addWidget( $w6 );
		
	}













}

