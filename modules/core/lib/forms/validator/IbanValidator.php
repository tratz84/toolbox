<?php

namespace core\forms\validator;


class IbanValidator extends BaseValidator {
    
    public function __construct() {
        
    }
    
    public function getMessage() { return 'Ongeldig IBAN-nummer'; }
    
    public function validate($widget) {
        $v = trim( $widget->getValue() );
        
        return validate_iban( $v );
    }
    
}

