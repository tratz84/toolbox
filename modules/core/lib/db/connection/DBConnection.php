<?php

namespace core\db\connection;

abstract class DBConnection {
    
    protected $error = null;
    
    public function __construct() {
        
    }
    
    public function getError() { return $this->error; }
    
    public abstract function connect();
    public abstract function disconnect();
    
    public abstract function ping();
    
    public abstract function beginTransaction();
    public abstract function commitTransaction();
    public abstract function rollbackTransaction();
    
    public abstract function createQueryBuilder();
    
}


