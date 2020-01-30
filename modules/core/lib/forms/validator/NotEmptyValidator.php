<?php


namespace core\forms\validator;


class NotEmptyValidator extends BaseValidator {
    
    
    public function __construct() {
        
    }
    
    public function getMessage() { return t('Field contains no value'); }
    
    public function validate($widget) {
        
        $v = trim( $widget->getValue() );
        
        if ($v == '')
            return false;
        else
            return true;
        
    }
    
}

