<?php


namespace core\forms\validator;


class NotZeroValidator extends BaseValidator {
    
    
    public function __construct() {
        
    }
    
    public function getMessage() { return t('Field contains no value'); }
    
    public function validate($widget) {
        
        $v = (int)trim( $widget->getValue() );
        
        if ($v == 0)
            return false;
        else
            return true;
                
    }
    
}

