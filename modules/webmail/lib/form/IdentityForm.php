<?php


namespace webmail\form;


use core\forms\BaseForm;
use core\forms\CheckboxField;
use core\forms\EmailField;
use core\forms\HiddenField;
use core\forms\TextField;
use core\forms\validator\EmailValidator;
use core\forms\validator\NotEmptyValidator;

class IdentityForm extends BaseForm {
    
    public function __construct() {
        parent::__construct();
        
        $this->addKeyField('identity_id');
        
        $this->addWidget( new HiddenField('identity_id', '', 'Id') );
        
        $this->addWidget( new CheckboxField('active', '', 'Actief'));
        $this->addWidget( new TextField('from_name', '', 'Naam') );
        $this->addWidget( new EmailField('from_email', '', 'E-mail'));
        
        
        $this->addValidator('from_name', new NotEmptyValidator());
        $this->addValidator('from_email', new EmailValidator());
        
    }
    
    
}