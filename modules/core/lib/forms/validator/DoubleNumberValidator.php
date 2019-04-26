<?php
namespace core\forms\validator;

use function validate_email;

class DoubleNumberValidator extends BaseValidator
{

    /**
     * $opts
     * - empty-allowed
     */
    public function __construct($opts = array()) {
        $this->opts = $opts;
    }

    public function getMessage() {
        return 'Ongeldig waarde';
    }

    public function validate($widget) {
        $v = trim($widget->getValue());

        if ($this->optionSet('empty-allowed') == false && $v == '')
            return true;

        if ($v == '' && $this->getOption('empty-allowed'))
            return true;

        $r1 = preg_match('/^\\d+$/', $v);
        $r2 = preg_match('/^\\d+\\.\\d+$/', $v);
        if (! $r1 && !$r2 )
            return false;

        $r = strtodouble($v);

        if ($this->optionSet('min')) {
            if ($v < $this->getOption('min'))
                return false;
        }

        if ($this->optionSet('max')) {
            if ($v > $this->getOption('max'))
                return false;
        }

        return true;
    }
}