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
        if ($v === null) $v = '';
        
        if (isset($this->opts['empty-allowed']) && $this->opts['empty-allowed'] && trim($v) == '')
            return true;
        
        return valid_date($v);
    }
    
}
