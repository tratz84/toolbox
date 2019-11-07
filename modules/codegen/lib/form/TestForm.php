<?php


namespace codegen\form;

use core\forms\BaseForm;
use core\forms\DefaultListEditWidget;
use core\forms\TextField;

class TestForm extends BaseForm {
    
    public function __construct() {
        parent::__construct();
        
        
        $lei = new DefaultListEditWidget('dunno');
        $lei->addWidget(new TextField('field1', '', 'Field1'));
        $lei->addWidget(new TextField('field2', '', 'Field2'));
        
        $this->addWidget( $lei );
    }
    
    
}