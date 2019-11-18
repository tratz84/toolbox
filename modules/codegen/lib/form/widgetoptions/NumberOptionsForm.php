<?php


namespace codegen\form\widgetoptions;



use core\forms\TextareaField;
use core\forms\NumberField;

class NumberOptionsForm extends DefaultWidgetOptionsForm {
    
    
    public function __construct() {
        parent::__construct();
        
        $this->addWidget(new NumberField('min', '', 'Min'));
        $this->addWidget(new NumberField('max', '', 'Max'));
        $this->addWidget(new NumberField('step', '', 'Step'));
    }
    
    
}
