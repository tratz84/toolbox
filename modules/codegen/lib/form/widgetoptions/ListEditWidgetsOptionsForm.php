<?php


namespace codegen\form\widgetoptions;


class ListEditWidgetsOptionsForm extends DefaultWidgetOptionsForm {
    
    public function __construct() {
        parent::__construct();
        
        $this->removeWidget('label');
        $this->removeWidget('defaultValue');
    }
    
}