<?php

namespace core\exception;

class OutOfBoundException extends \Exception {
    
    public function __construct($message = null, $code = 0, \Exception $previous = null) {
        parent::__construct($message, $code, $previous);
    }
    
    
}
