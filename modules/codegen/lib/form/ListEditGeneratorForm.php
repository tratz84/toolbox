<?php

namespace codegen\form;



use core\forms\validator\NotEmptyValidator;

class ListEditGeneratorForm extends \core\forms\CodegenBaseForm {

	public function __construct() {
		
		parent::__construct();
		
		$this->codegen();
		
		
		$this->addValidator('module_name', new NotEmptyValidator());
		$this->addValidator('name', new NotEmptyValidator());
		$this->addValidator('name', function($form) {
		    $n = $form->getWidgetValue('name');
		    
		    if (endsWith($n, 'ListEdit') == false) {
		        return 'name must end with ListEdit';
		    }
		});
	}
	
	
	
	public function codegen() {
		$func1 = function() {  
		
		return codegen_map_modules();
		 }; 
		$func2 = function() {  return codegen_map_dao_classes(); }; 
		
		$w1 = new \core\forms\HiddenField('data', NULL, t(''));
		$this->addWidget( $w1 );
		$w2 = new \core\forms\SelectField('module_name', NULL, $func1(), t('Module'));
		$this->addWidget( $w2 );
		$w3 = new \core\forms\TextField('name', NULL, t('Name'));
		$this->addWidget( $w3 );
		$w4 = new \core\forms\SelectField('daoObject', NULL, $func2(), t('DAO Object'));
		$this->addWidget( $w4 );
		$w5 = new \core\forms\TextField('short_description', NULL, t('Short description'));
		$this->addWidget( $w5 );
		$w6 = new \core\forms\TextField('objects_getter', NULL, t('Objects getter-name'));
		$this->addWidget( $w6 );
		$w7 = new \core\forms\CheckboxField('no_results_message', NULL, t('No-results message'));
		$this->addWidget( $w7 );
		$w7->setInfoText( 'Show \'no results found\'-message when table is empty' );
		
	}


















}

