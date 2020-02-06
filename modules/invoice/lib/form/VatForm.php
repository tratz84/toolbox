<?php

namespace invoice\form;


use core\forms\BaseForm;
use core\forms\CheckboxField;
use core\forms\HiddenField;
use core\forms\PercentageField;
use core\forms\TextField;
use core\forms\validator\NotEmptyValidator;
use core\forms\validator\PercentageValidator;

class VatForm extends BaseForm {
    
    public function __construct() {
        parent::__construct();
        
        $this->addKeyField('vat_id');
        
        $this->addWidget( new HiddenField('vat_id', '', 'Id') );
        
        $this->addWidget( new CheckboxField('visible', '', 'Zichtbaar'));
        $this->addWidget( new CheckboxField('default_selected', '', 'Standaard gekozen'));
        $this->addWidget( new TextField('description', '', 'Omschrijving') );
        $this->addWidget( new PercentageField('percentage', '', 'Percentage') );
        
        $this->addValidator('description', new NotEmptyValidator());
        $this->addValidator('percentage', new NotEmptyValidator());
        $this->addValidator('percentage', new PercentageValidator());
        
    }
    
}

