<?php


namespace core\forms\validator;

class DateTimeValidator extends BaseValidator {
    
    /**
     * $opts
     * - empty-allowed
     */
    public function __construct($opts = array()) {
        $this->opts = $opts;
    }
    
    public function getMessage() { return 'Ongeldig datum/tijd'; }
    
    public function validate($widget) {
        $v = $widget->getValue();
        
        return valid_datetime($v);
    }
    
}
