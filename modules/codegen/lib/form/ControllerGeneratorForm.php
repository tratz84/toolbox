<?php

namespace codegen\form;

use core\forms\validator\NotEmptyValidator;

class ControllerGeneratorForm extends \core\forms\CodegenBaseForm {

	public function __construct() {
		
		parent::__construct();
		
		$this->codegen();
		
		
		$this->addValidator('module_name', new NotEmptyValidator());
		$this->addValidator('controller_name', function($form) {
		    $cn = trim( $form->getWidgetValue('controller_name') );
		    
		    if ($cn == '') {
		        return 'required';
		    }
		    
		    if (preg_match('/^[a-zA-Z0-9\\/]+$/', $cn) == false) {
		        return 'invalid controller name';
		    }
		    if (strpos($cn, '/') === 0 || endsWith($cn, '/') == true) {
		        return 'name can\'t start/end with a slash';
		    }
		    
		    $module_name = $form->getWidgetValue('module_name');
		    if ($module_name) {
    		    $f = module_file($module_name, 'controller/'.$cn.'Controller.php');
    		    if ($f != false) {
    		        return 'Controller already exists';
    		    }
		    }
		    
		    // TODO: add other checks, like classnames can't start with number, etc..?
		    //       well, it's a development tool, ppl prolly know the basic php-rules... :)
		    
		});
	}
	
	
	
	public function codegen() {
		$func1 = function() {  
		
		$map = array();
		$map[''] = 'Make your choice';
		foreach(module_list() as $m => $p) {
		$map[$m] = $m;
		}
		
		return $map;
		 }; 
		
		$w1 = new \core\forms\SelectField('module_name', NULL, $func1(), 'Module');
		$this->addWidget( $w1 );
		$w2 = new \core\forms\TextField('controller_name', NULL, 'Controller name');
		$this->addWidget( $w2 );
		$w2->setInfoText( 'name may contain slashes' );
		$w3 = new \core\forms\TextareaField('default_actions', NULL, 'Default actions');
		$this->addWidget( $w3 );
		$w3->setInfoText( 'default action_-functions + templates created for controller' );
		$w3->setValue( "index\nsearch\nedit\ndelete" );
		
	}



















}

