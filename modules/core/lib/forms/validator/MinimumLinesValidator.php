<?php


namespace core\forms\validator;

use function validate_email;


class MinimumLinesValidator extends BaseValidator {
    
    
    /**
     * $opts
     * - empty-allowed
     */
    public function __construct($opts = array()) {
        $this->opts = $opts;
        
        if (isset($this->opts['lines']) == false)
            $this->opts['lines'] = 1;
    }
    
    public function getMessage() { return 'Minimaal ' . intval($this->opts['lines']) . ' record(s) benodigd'; }
    
    public function validate($widget) {
        
        if (count( $widget->getObjects() ) < intval($this->opts['lines'])) {
            return false;
        }
        
        return true;
    }
    
    
    
}