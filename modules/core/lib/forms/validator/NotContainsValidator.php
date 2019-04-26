<?php


namespace core\forms\validator;


class NotContainsValidator extends BaseValidator {
    
    protected $values = array();
    
    public function __construct($values) {
        $this->values = $values;
    }
    
    public function getMessage() { return 'Veld bevat geen waarde'; }
    
    public function validate($widget) {
        
        $v = trim( $widget->getValue() );
        
        if (in_array($v, $this->values))
            return false;
        else
            return true;
                
    }
    
}

