<?php

namespace customer\forms;

use core\forms\BaseForm;
use core\forms\CheckboxField;
use core\forms\HiddenField;
use core\forms\TextField;
use core\forms\validator\NotEmptyValidator;

class CompanyTypeForm extends BaseForm {
    
    
    
    public function __construct() {
        parent::__construct();
        
        $this->addKeyField('company_type_id');
        
        $this->addWidget( new HiddenField('company_type_id', '', 'Id') );
        
        $this->addWidget( new TextField('type_name', '', t('Companyname')) );
        $this->addWidget( new CheckboxField('default_selected', '', t('Default selected')));
        
        $this->addValidator('type_name', new NotEmptyValidator());
    }
    
}
