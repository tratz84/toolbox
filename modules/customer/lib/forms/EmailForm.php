<?php


namespace customer\forms;


use core\forms\BaseForm;
use core\forms\CheckboxField;
use core\forms\EmailField;
use core\forms\TextField;
use core\forms\validator\EmailValidator;

class EmailForm extends BaseForm {
    
    
    
    public function __construct() {
        parent::__construct();
        
        $this->addWidget(new EmailField('email_address', '', t('Email')));
        $this->addWidget(new TextField('note', '', t('Note')));
        $this->addWidget(new CheckboxField('primary_address', '', t('Primary')));
        
        $this->addValidator('email_address', new EmailValidator());
    }
    
    
}