<?php

namespace core\exception;

class FileException extends \Exception {
    
    protected $query = null;
    
    public function __construct($message = null, $code = 0, \Exception $previous = null) {
        parent::__construct($message, $code, $previous);
    }
    
}

