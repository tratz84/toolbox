<?php


namespace core\forms\validator;

use function validate_email;


class PercentageValidator extends BaseValidator {
    
    
    /**
     * $opts
     * - empty-allowed
     */
    public function __construct($opts = array()) {
        $this->opts = $opts;
    }
    
    public function getMessage() { return 'Ongeldig waarde'; }
    
    public function validate($widget) {
        $v = trim( $widget->getValue() );
        
        if ($this->optionSet('empty-allowed') == false && $v == '')
            return true;
            
        if ($v == '' && $this->getOption('empty-allowed'))
            return true;
        
        // remove percentage & spaces
        $v = str_replace(array('%', ' '), '', $v);
        
        return preg_match('/^\\d+[,\\.]\\d+$/', $v) || preg_match('/^\\d+$/', $v);
    }
    
    
    
}