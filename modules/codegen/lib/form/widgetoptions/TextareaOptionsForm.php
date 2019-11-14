<?php


namespace codegen\form\widgetoptions;



use core\forms\TextareaField;

class TextareaOptionsForm extends DefaultWidgetOptionsForm {
    
    
    public function __construct() {
        parent::__construct();
        
        
        $this->removeWidget('defaultValue');
        
        $ta = new TextareaField('defaultValue', '', 'Default value');
        $this->addWidget($ta);
    }
    
    
}
