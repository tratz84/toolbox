<?php

namespace core\parser;


class ArgumentParser {
    
    protected $tokens = array();
    
    protected $options = array();
    
    
    public function __construct($str, $skip=1) {
        $this->str = '';
        
        if (is_array($str)) {
            foreach($str as $t) {
                $this->tokens[] = $t;
            }
        } else {
            $toks = explode(' ', $str);
            foreach($toks as $t) {
                $this->tokens[] = $t;
            }
        }
        
        $this->parse();
    }
    
    
    protected function parse() {
        for($x=0; $x < count($this->tokens); $x++) {
            $t = $this->tokens[$x];
            
            $t = trim($t);
            
            if (strpos($t, '--') === 0 && strlen($t) > 2) {
                if (strlen($t) > 2) {
                    $key = trim( trim(substr($t, 3)) );
                    $this->options[$key] = true;
                }
            }
            else if (strpos($t, '-') === 0 && strlen($t) > 1) {
                if (strlen($t) > 1) {
                    $c = $t{1};
                    $this->options[$c] = trim(substr($t, 2));
                    $key = trim( substr($t, 1) );
                    if ($key)
                        $this->options[$key] = true;
                }
            }
        }
    }
    
    public function getOptions() { return $this->options; }
    
    public function hasOption($optionName) {
        return array_key_exists($optionName, $this->options) ? true : false;
    }
    
}

