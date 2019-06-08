<?php

namespace core\db;


abstract class QueryBuilder {
    
    protected $resourceName;
    
    protected $selectFields;
    protected $updateFields = array();
    protected $table;
    protected $leftJoin = array();
    protected $rightJoin = array();
    protected $join = array();
    
    /**
     * @var QueryBuilderWhereContainer
     */
    protected $whereContainer;
    
    protected $start = 0;
    protected $limit = -1;
    
    protected $orderBy = null;
    
    
    
    public function __construct($resourceName) {
        $this->resourceName = $resourceName;
        
        $this->whereContainer = new QueryBuilderWhereContainer();
        
    }
    
    public function setUpdateField($fieldName, $value) {
        $this->updateFields[$fieldName] = $value;
    }
    
    public abstract function createSelect();
    public abstract function getParams();
    
    
    
    /**
     * setFields() - set fields to query, possible methods,
     *      setFields(array('tbl.field1', 'tbl.field2'))
     *      setFields('tbl.field1', 'field2)
     */
    public function selectFields() {
        $arr = func_get_args();
        foreach($arr as $a) {
            if (is_array($a) == false)
                $a = array( $a );
            
            foreach($a as $b) {
                $fieldName = null;
                $tableName = null;
                
                if (strpos($b, '.') !== false) {
                    list($fieldName, $tableName) = explode('.', $b, 2);
                } else {
                    $fieldName = $b;
                }
                
                $this->addField($fieldName, $tableName);
            }
        }
    }
    public function getSelectFields() { return $this->selectFields; }
    public function selectField($fieldName, $tableName=null) {
        $this->selectFields[$fieldName] = array(
            'field' => $fieldName,
            'tableName' => $tableName
        );
    }
    public function clearSelectFields() { $this->selectFields = array(); }
    
    
    public function setTable($table) { $this->table = $table; }
    public function getTable() { return $this->table; }
    
    public function setStart($s) { $this->start = (int)$s; }
    public function getStart() { return $this->start; }
    
    public function setLimit($l) { $this->limit = $l; }
    public function getLimit() { return $this->limit; }
    
    
    public function join($table, $fieldJoinTable, $parentTable=null, $fieldParentTable=null) {
        if ($fieldParentTable == null)
            $fieldParentTable = $fieldJoinTable;
            
            $this->join[] = array(
                'table' => $table,
                'fieldJoinTable' => $fieldJoinTable,
                'parentTable' => $parentTable,
                'fieldParentTable' => $fieldParentTable
            );
    }
    
    public function leftJoin($table, $fieldJoinTable, $parentTable=null, $fieldParentTable=null) {
        if ($fieldParentTable == null)
            $fieldParentTable = $fieldJoinTable;
        
        $this->leftJoin[] = array(
            'table' => $table,
            'fieldJoinTable' => $fieldJoinTable,
            'parentTable' => $parentTable,
            'fieldParentTable' => $fieldParentTable
        );
    }
    
    public function rightJoin($table, $fieldJoinTable, $parentTable=null, $fieldParentTable=null) {
        if ($fieldParentTable == null)
            $fieldParentTable = $fieldJoinTable;
            
            $this->rightJoin[] = array(
                'table' => $table,
                'fieldJoinTable' => $fieldJoinTable,
                'parentTable' => $parentTable,
                'fieldParentTable' => $fieldParentTable
            );
    }
    
    
    public function addWhere(QueryBuilderWhere $qbw) {
        $this->whereContainer->addWhere( $qbw );
    }
    
    
    public function setOrderBy($o) { $this->orderBy = $o; }
    public function getOrderBy() { return $this->orderBy; }
    
    
}

