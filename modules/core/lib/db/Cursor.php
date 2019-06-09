<?php

namespace core\db;


abstract class Cursor {
    
    protected $objectName;
    
    public function __construct($objectName) {
        $this->objectName = $objectName;
    }
    
    
    public abstract function numRows();
    public abstract function current();
    
    public abstract function hasNext();
    public abstract function moveTo($no);
    
    public abstract function next();
    
    
    public abstract function free();
    
    
}