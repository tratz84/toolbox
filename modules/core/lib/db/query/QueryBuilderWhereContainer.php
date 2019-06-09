<?php

namespace core\db\query;

use core\exception\InvalidStateException;

class QueryBuilderWhereContainer {
    
    protected $joinMethod = 'AND';
    protected $where = array();
    
    
    public function __construct($joinMethod='AND') {
        $this->setJoinMethod($joinMethod);
    }
    
    public function getJoinMethod() { return $this->joinMethod; }
    public function setJoinMethod($m) {
        $m = strtoupper(trim($m));
        
        if ($m != 'AND' && $m != 'OR') {
            throw new InvalidStateException('Invalid join method');
        }
        
        $this->joinMethod = $m;
    }
    
    public function getWhere() {
        return $this->where;
    }
    
    /**
     * 
     * @param QueryBuilderWhere | QueryBuilderWhereContainer $qbw
     */
    public function addWhere($qbw) {
        $this->where[] = $qbw;
    }
    
    
}