<?php


namespace core\forms\validator;

use function validate_email;


class HexColorValidator extends BaseValidator {
    
    
    /**
     * $opts
     * - empty-allowed
     */
    public function __construct($opts = array()) {
        $this->opts = $opts;
    }
    
    public function getMessage() { return 'Ongeldig kleur'; }
    
    public static function validateHexColor($str) {
        if (preg_match('/^#{0,1}[0-9a-fA-F]{3}$/', $str) || preg_match('/^#{0,1}[0-9a-fA-F]{6}$/', $str)) {
            return true;
        } else {
            return false;
        }
    }
    
    public function validate($widget) {
        $v = trim( $widget->getValue() );
        
        if ($v == '' && $this->getOption('empty-allowed'))
            return true;
        
        if (self::validateHexColor($v)) {
            return true;
        } else {
            return false;
        }
    }
    
    
}
