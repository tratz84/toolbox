<?php

namespace invoice\form;


use core\forms\BaseForm;
use core\forms\DatePickerField;
use core\forms\EuroField;

class PriceAdjustmentForm extends BaseForm {
    
    
    public function __construct() {
        
        
        $this->addWidget(new DatePickerField('start_date', '', 'Startdatum'));
        
        $this->addWidget(new EuroField('new_price',    '', 'Prijs'));
        $this->addWidget(new EuroField('new_discount', '', 'Korting'));
        
    }
}
