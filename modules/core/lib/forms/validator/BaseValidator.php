<?php


namespace core\forms\validator;


abstract class BaseValidator {
    
    protected $opts = array();
    
    
    public function optionSet($name) {
        return isset($this->opts[$name]);
    }
    public function getOption($name, $defaultVal=false) {
        if (isset($this->opts[$name]))
            return $this->opts[$name];
        else
            return $defaultVal;
    }
    
    
    public abstract function getMessage();
    public abstract function validate($widget);
    
}

