<?php

namespace base\forms;


use core\forms\BaseForm;
use core\forms\TelField;
use core\forms\TextField;
use core\forms\validator\NotEmptyValidator;

class PhoneForm extends BaseForm {
    
    
    public function __construct() {
        parent::__construct();
        
        $this->addWidget(new TelField('phonenr', '', 'Telefoonnummer'));
        $this->addWidget(new TextField('note', '', 'Notitie'));
        
        
        $this->addValidator('phonenr', new NotEmptyValidator());
    }
    
}

