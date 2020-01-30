<?php


namespace codegen\form\widgetoptions;



class CheckboxOptionsForm extends DefaultWidgetOptionsForm {
    
    
    public function __construct() {
        parent::__construct();
        
        
        $this->removeWidget('defaultValue');
    }
    
    
}
