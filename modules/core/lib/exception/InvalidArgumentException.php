<?php

namespace core\exception;

class InvalidArgumentException extends \Exception {
    
    public function __construct(?string $message = null, $code = 0, ?\Exception $previous = null) {
        parent::__construct($message, $code, $previous);
    }
    
    
}

