<?php


namespace codegen\form\widgetoptions;



use core\forms\TimePickerField;

class TimePickerOptionsForm extends DefaultWidgetOptionsForm {
    
    
    public function __construct() {
        parent::__construct();
        
        $this->removeWidget('defaultValue');
        $this->addWidget(new TimePickerField('defaultValue', '', t('Default time')));
    }
    
}
