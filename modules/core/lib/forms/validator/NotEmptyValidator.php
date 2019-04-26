<?php


namespace core\forms\validator;


class NotEmptyValidator extends BaseValidator {
    
    
    public function __construct() {
        
    }
    
    public function getMessage() { return 'Veld bevat geen waarde'; }
    
    public function validate($widget) {
        
        $v = trim( $widget->getValue() );
        
        if ($v == '')
            return false;
        else
            return true;
        
    }
    
}

