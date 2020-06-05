<?php

namespace codegen\form;

use core\forms\validator\NotEmptyValidator;

class ListFormGeneratorForm extends \core\forms\CodegenBaseForm {

	public function __construct() {
		
		parent::__construct();
		
		$this->codegen();
		
		
		$this->addValidator('module_name', new NotEmptyValidator());
		$this->addValidator('name', new NotEmptyValidator());
		$this->addValidator('name', function($form) {
		    $n = $form->getWidgetValue('name');
		    
		    if (endsWith($n, 'ListForm') == false) {
		        return 'name must end with ListForm';
		    }
		});
	}
	
	
	
	public function codegen() {
		$func1 = function() {  return codegen_map_modules(); }; 
		$func2 = function() {  return codegen_map_dao_classes(); }; 
		$func3 = function() {  return array_merge(array('' => 'Make your choice'), codegen_map_form_classes());
		 }; 
		
		$w1 = new \core\forms\HiddenField('data', NULL, t('Hidden field'));
		$this->addWidget( $w1 );
		$w2 = new \core\forms\SelectField('module_name', NULL, $func1(), t('Module'));
		$this->addWidget( $w2 );
		$w3 = new \core\forms\TextField('name', NULL, t('Name'));
		$this->addWidget( $w3 );
		$w4 = new \core\forms\SelectField('daoObject', NULL, $func2(), t('DAO Object'));
		$this->addWidget( $w4 );
		$w5 = new \core\forms\TextField('short_description', NULL, t('Short description'));
		$this->addWidget( $w5 );
		$w6 = new \core\forms\SelectField('form_class', NULL, $func3(), t('Form'));
		$this->addWidget( $w6 );
		$w7 = new \core\forms\TextField('label', NULL, t('Label'));
		$this->addWidget( $w7 );
		$w7->setInfoText( 'Label shown in form where widget is used' );
		$w8 = new \core\forms\CheckboxField('sortable', NULL, t('Sortable'));
		$this->addWidget( $w8 );
		$w9 = new \codegen\form\ListFormFieldListEdit();
		$w9->setName( 'fields' );
		$w9->setMethodObjectList( 'fields' );
		$this->addWidget( $w9 );
		$w10 = new \codegen\form\ListFormPublicFieldListEdit();
		$w10->setName( 'publicfields' );
		$w10->setMethodObjectList( 'publicfields' );
		$this->addWidget( $w10 );
		
	}






















}

