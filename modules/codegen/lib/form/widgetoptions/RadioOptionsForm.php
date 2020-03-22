<?php

namespace codegen\form\widgetoptions;


use core\forms\TextareaField;

class RadioOptionsForm extends DefaultWidgetOptionsForm {
    
    public function __construct() {
        parent::__construct();
        
        
        $this->addWidget(new TextareaField('optionItems', '', 'Options'));
    }
    
}
