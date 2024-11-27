<?php

namespace core\db\query;


class QueryBuilderWhereRaw {
    
    
    protected $sqlWhere;
    
    
    
    public function __construct( $sqlWhere ) {
        $this->sqlWhere = $sqlWhere;
    }
    
    public function getSqlWhere() { return $this->sqlWhere; }
    public function setSqlWhere($sql) { $this->sqlWhere = $sql; }
    
    
    
}

