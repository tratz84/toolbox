<?php

namespace filesync\exception;

class FilesyncException extends \Exception {
    
    protected $storeFileId = null;
    
    public function __construct($message = null, $code = 0, \Exception $previous = null) {
        parent::__construct($message, $code, $previous);
    }
    
    
    
    public function getStoreFileId() { return $this->storeFileId; }
    public function setStoreFileId($i) { $this->storeFileId = $i; }
    
    
    
}

