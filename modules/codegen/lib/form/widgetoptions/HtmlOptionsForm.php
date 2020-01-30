<?php


namespace codegen\form\widgetoptions;



use core\forms\TextField;

class HtmlOptionsForm extends DefaultWidgetOptionsForm {
    
    
    public function __construct() {
        parent::__construct();
        
        
        $this->removeWidget('defaultValue');
        
        $ta = new TextField('value', '', 'Value');
        $this->addWidget($ta);
    }
    
    
}
