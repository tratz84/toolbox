<?php


namespace core\forms\validator;

class DateValidator extends BaseValidator {
    
    /**
     * $opts
     * - empty-allowed
     */
    public function __construct($opts = array()) {
        $this->opts = $opts;
    }
    
    public function getMessage() { return t('Invalid date'); }
    
    public function validate($widget) {
        $v = $widget->getValue();
        
        return valid_date($v);
    }
    
}
