<?php

namespace core\forms\validator;


use core\forms\SelectField;
use core\forms\RadioField;

class NotFirstOptionValidator extends BaseValidator {
    
    public function __construct() {
        
    }
    
    public function getMessage() { return 'Ongeldige waarde gekozen'; }
    
    public function validate($widget) {
        if (is_a($widget, SelectField::class) || is_a($widget, RadioField::class)) {
            $keys = array_keys( $widget->getOptionItems() );
            
            if (count($keys) > 0 && $keys[0] == $this->getValue()) {
                return false;
            }
        }
        
        
        return true;
    }
    
}

