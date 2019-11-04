<?php

namespace codegen\form\widgetoptions;


use core\forms\TextareaField;

class SelectOptionsForm extends DefaultWidgetOptionsForm {
    
    public function __construct() {
        parent::__construct();
        
        
        $this->addWidget(new TextareaField('options', '', 'Options'));
    }
    
}
