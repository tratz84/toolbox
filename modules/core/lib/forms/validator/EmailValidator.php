<?php


namespace core\forms\validator;

use function validate_email;


class EmailValidator extends BaseValidator {
    
    
    /**
     * $opts
     * - empty-allowed
     */
    public function __construct($opts = array()) {
        $this->opts = $opts;
    }
    
    public function getMessage() { return 'Ongeldig e-mailadres'; }
    
    public function validate($widget) {
        $v = trim( $widget->getValue() );
        
        if ($v == '' && $this->getOption('empty-allowed'))
            return true;
        
        return validate_email($v);
    }
    
    
    
}