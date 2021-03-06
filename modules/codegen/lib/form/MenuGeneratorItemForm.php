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
		
		
		$w1 = new \core\forms\SelectField('icon', NULL, array (
		  'fa-tags' => 'fa-tags',
		  'fa-tasks' => 'fa-tasks',
		  'fa-calendar' => 'fa-calendar',
		  'fa-sign-out' => 'fa-sign-out',
		  'fa-bug' => 'fa-bug',
		  'fa-bars' => 'fa-bars',
		  'fa-file-archive-o' => 'fa-file-archive-o',
		  'fa-dashboard' => 'fa-dashboard',
		  'fa-user' => 'fa-user',
		  'fa-share-alt' => 'fa-share-alt',
		  'fa-money' => 'fa-money',
		  'fa-signal' => 'fa-signal',
		  'fa-wrench' => 'fa-wrench',
		  'fa-file' => 'fa-file',
		), t('Icon'));
		$this->addWidget( $w1 );
		$w2 = new \core\forms\TextField('label', NULL, t('Label'));
		$this->addWidget( $w2 );
		$w3 = new \core\forms\TextField('url', NULL, t('Url'));
		$this->addWidget( $w3 );
		$w4 = new \core\forms\NumberField('weight', NULL, t('Weight'));
		$this->addWidget( $w4 );
		$w5 = new \core\forms\CheckboxField('as_first_child', NULL, t('As first child'));
		$this->addWidget( $w5 );
		$w5->setInfoText( 'In case of a submenu, this menu-item on top?' );
		$w6 = new \core\forms\TextareaField('menuPermission', NULL, t('Permission'));
		$this->addWidget( $w6 );
		
	}












}

