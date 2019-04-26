<?php

namespace core\exception;

class DatabaseException extends \Exception {
    
    protected $query = null;
    
    public function __construct($message = null, $code = 0, \Exception $previous = null) {
        parent::__construct($message, $code, $previous);
    }
    
    public function setQuery($query) { $this->query = $query; }
    public function getQuery() { return $this->query; }
    
}

