<?php


namespace core\forms\validator;


class NotContainsValidator extends BaseValidator {
    
    protected $values = array();
    
    public function __construct($values) {
        $this->values = $values;
    }
    
    public function getMessage() { return t('Field contains no value'); }
    
    public function validate($widget) {
        
        $v = trim( $widget->getValue() );
        
        if (in_array($v, $this->values))
            return false;
        else
            return true;
                
    }
    
}

