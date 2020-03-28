<?php


namespace codegen\form\widgetoptions;



use core\forms\CheckboxField;
use core\forms\RadioField;

class DatePickerOptionsForm extends DefaultWidgetOptionsForm {
    
    
    public function __construct() {
        parent::__construct();
        
        
//         $this->removeWidget('defaultValue');
        $this->addWidget(new RadioField('show_weeks', '', [1=>'Yes',0=>'No'], 'Show weeks'));
    }
    
    public function generateExtraSetters($varname) {
        $code = '';
        
        if ($this->getWidgetValue('show_weeks')) {
            $code .= $varname . '->setShowWeeks( true );' . PHP_EOL;
        }
        
        return $code;
    }
    
}
