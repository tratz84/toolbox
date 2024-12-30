<?php

namespace core\db\query;

use core\exception\InvalidArgumentException;



class QueryBuilderWhereRaw {
    
    
    protected $sqlWhere;
    protected $params = array();
    
    
    
    public function __construct( $sqlWhere, $params=array() ) {
        $this->sqlWhere = $sqlWhere;
        
        $this->setParams($params);
    }
    
    public function getSqlWhere() { return $this->sqlWhere; }
    public function setSqlWhere($sql) { $this->sqlWhere = $sql; }
    
    
    public function setParams($params) {
        if (is_array($params) == false)
            throw new InvalidArgumentException( '$params not an array' );
        
        $this->params = $params;
    }
    public function getParams() { return $this->params; }
    public function resetParams() { $this->params = array(); }
    
    
    
    
}

