<?php


namespace base\forms;


use core\forms\BaseForm;
use core\forms\CheckboxField;
use core\forms\EmailField;
use core\forms\TextField;
use core\forms\validator\EmailValidator;

class EmailForm extends BaseForm {
    
    
    
    public function __construct() {
        parent::__construct();
        
        $this->addWidget(new EmailField('email_address', '', 'E-mail'));
        $this->addWidget(new TextField('note', '', 'Notitie'));
        $this->addWidget(new CheckboxField('primary_address', '', 'Primary'));
        
        $this->addValidator('email_address', new EmailValidator());
    }
    
    
}