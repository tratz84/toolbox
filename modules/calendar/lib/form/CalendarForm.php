<?php


namespace calendar\form;


use core\forms\BaseForm;
use core\forms\HiddenField;
use core\forms\TextField;
use core\forms\CheckboxField;
use core\forms\validator\NotEmptyValidator;

class CalendarForm extends BaseForm {
    
    
    public function __construct() {
        $this->addWidget( new HiddenField('calendar_id', '', 'Id') );
        
        $this->addWidget( new CheckboxField('active', '', 'Actief') );
        $this->addWidget( new TextField('name', '', 'Naam') );
        
        $this->addValidator('name', new NotEmptyValidator());
    }
    
    
}