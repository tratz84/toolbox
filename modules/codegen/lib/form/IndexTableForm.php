<?php

namespace codegen\form;

use core\forms\validator\NotEmptyValidator;

class IndexTableForm extends \core\forms\BaseForm {

	public function __construct() {
		
		parent::__construct();
		
		$this->codegen();
		
		$this->addValidator('module_name', new NotEmptyValidator());
		$this->addValidator('controller_name', function($form) {
		    $controller_name = $form->getWidgetValue('controller_name');
		    
		    if (endsWith($controller_name, 'Controller') == false) {
		        return 'name must end with "Controller"';
		    }
		});
	}
	
	
	
	public function codegen() {
		$func1 = function() {  return codegen_map_modules(); }; 
		$func2 = function() {  return codegen_map_dao_classes(); }; 
		
		$w1 = new \core\forms\SelectField('module_name', NULL, $func1(), 'Module');
		$this->addWidget( $w1 );
		$w2 = new \core\forms\SelectField('dao_class', NULL, $func2(), 'DAO Class');
		$this->addWidget( $w2 );
		$w3 = new \core\forms\TextField('controller_name', NULL, 'Controller name');
		$this->addWidget( $w3 );
		$w4 = new \core\forms\TextareaField('query', NULL, 'Query');
		$this->addWidget( $w4 );
		$w5 = new \core\forms\TextareaField('phpcode', NULL, 'Php Code');
		$this->addWidget( $w5 );
		$w5->setInfoText( 'Php-code for rendering $row[]' );
		$w6 = new \core\forms\HtmlField('html_cols', NULL, 'HTML Columns');
		$this->addWidget( $w6 );
		$w6->setInfoText( 'HTML rendering code' );
		$w7 = new \codegen\form\IndexTableColumnListEdit();
		$w7->setName( 'htmlColumns' );
		$w7->setMethodObjectList( 'htmlColumns' );
		$this->addWidget( $w7 );
		$w8 = new \core\forms\HtmlField('html_col_web', NULL, 'Columns web-interface');
		$this->addWidget( $w8 );
		$w8->setInfoText( 'Columns shown in the webinterface' );
		$w9 = new \codegen\form\ListFormFieldListEdit();
		$w9->setName( 'webColumns' );
		$w9->setMethodObjectList( 'webColumns' );
		$this->addWidget( $w9 );
		$w10 = new \core\forms\HtmlField('html_colexport', NULL, 'Export Excel');
		$this->addWidget( $w10 );
		$w10->setInfoText( 'Columns shown in exports like Excel & DataSource' );
		$w11 = new \codegen\form\ListFormFieldListEdit();
		$w11->setName( 'exportColumns' );
		$w11->setMethodObjectList( 'exportColumns' );
		$this->addWidget( $w11 );
		
	}






















}

