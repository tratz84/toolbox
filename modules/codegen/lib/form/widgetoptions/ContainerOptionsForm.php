<?php


namespace codegen\form\widgetoptions;


class ContainerOptionsForm extends DefaultWidgetOptionsForm {
    
    
    public function __construct() {
        parent::__construct();
        
        $this->getWidget('name')->setValue('container');
        
        $this->removeWidget('label');
        $this->removeWidget('defaultValue');
        
    }
    
}
