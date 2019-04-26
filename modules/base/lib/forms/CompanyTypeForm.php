<?php

namespace base\forms;

use core\forms\BaseForm;
use core\forms\CheckboxField;
use core\forms\HiddenField;
use core\forms\TextField;
use core\forms\validator\NotEmptyValidator;

class CompanyTypeForm extends BaseForm {
    
    
    
    public function __construct() {
        
        $this->addKeyField('company_type_id');
        
        $this->addWidget( new HiddenField('company_type_id', '', 'Id') );
        
        $this->addWidget( new TextField('type_name', '', 'Bedrijfsnaam') );
        $this->addWidget( new CheckboxField('default_selected', '', 'Standaard geselecteerd'));
        
        $this->addValidator('type_name', new NotEmptyValidator());
    }
    
}
