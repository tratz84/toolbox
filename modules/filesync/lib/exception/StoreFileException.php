<?php

namespace filesync\exception;

class StoreFileException extends FilesyncException {
    
    
    public function __construct($message = null, $code = 0, \Exception $previous = null) {
        parent::__construct($message, $code, $previous);
    }
    
}

