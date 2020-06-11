<?php

namespace core\db\query;


use core\db\connection\DBConnection;

abstract class QueryBuilder {
    
    protected $dbconnection;
    
    protected $selectFields = array();
    protected $selectFunctions = array();
    
    protected $fieldValues = array();
    protected $table;
    protected $leftJoin = array();
    protected $rightJoin = array();
    protected $join = array();
    
    protected $objectName = null;
    
    /**
     * @var QueryBuilderWhereContainer
     */
    protected $whereContainer;
    
    protected $start = 0;
    protected $limit = -1;
    
    protected $orderBy = null;
    protected $groupBy = null;
    
    
    
    public function __construct(DBConnection $dbconnection) {
        $this->dbconnection = $dbconnection;
        
        $this->whereContainer = new QueryBuilderWhereContainer();
        
    }
    
    public function getConnection() { return $this->dbconnection; }
    
    public function setObjectName($n) { $this->objectName = $n; }
    public function getObjectName() { return $this->objectName; }
    
    
    public function setFieldValue($fieldName, $value) {
        $this->fieldValues[$fieldName] = $value;
        
        return $this;
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
                $label = null;
                
                if (strpos($b, ' ') !== false) {
                    list($left, $right) = preg_split('/\\s+/', $b, 2);
                    
                    $b = $left;
                    $label = $right;
                }
                
                if (strpos($b, '.') !== false) {
                    list($tableName, $fieldName) = explode('.', $b, 2);
                } else {
                    $fieldName = $b;
                }
                
                $this->selectField($fieldName, $tableName, $label);
            }
        }
        return $this;
    }
    public function getSelectFields() { return $this->selectFields; }
    public function selectField($fieldName, $tableName=null, $label=null) {
        if ($tableName) {
            $key = $tableName . '.' . $fieldName;
        } else {
            $key = $fieldName;
        }
        
        $this->selectFields[$key] = array(
            'field'     => $fieldName,
            'tableName' => $tableName,
            'label'     => $label
        );
        
        return $this;
    }
    public function clearSelectFields() {
        $this->selectFields = array();
        $this->selectFunctions = array();
    }
    
    // used for count(), max(), etc
    public function selectFunction($func) {
        $this->selectFunctions[] = $func;
    }
    
    
    public function setTable($table) {
        $this->table = $table;
        return $this;
    }
    public function getTable() { return $this->table; }
    
    public function setStart($s) {
        $this->start = (int)$s;
        return $this;
    }
    public function getStart() { return $this->start; }
    
    public function setLimit($l) {
        $this->limit = $l;
        return $this;
    }
    public function getLimit() { return $this->limit; }
    
    
    public function join($table, $fieldJoinTable, $parentTable=null, $fieldParentTable=null) {
        if ($fieldParentTable == null)
            $fieldParentTable = $fieldJoinTable;
        if ($parentTable == null)
            $parentTable = $this->table;
        
        $this->join[] = array(
            'table' => $table,
            'fieldJoinTable' => $fieldJoinTable,
            'parentTable' => $parentTable,
            'fieldParentTable' => $fieldParentTable
        );
        
        return $this;
    }
    
    public function leftJoin($table, $fieldJoinTable, $parentTable=null, $fieldParentTable=null) {
        if ($fieldParentTable == null)
            $fieldParentTable = $fieldJoinTable;
        if ($parentTable == null)
            $parentTable = $this->table;
        
        $this->leftJoin[] = array(
            'table' => $table,
            'fieldJoinTable' => $fieldJoinTable,
            'parentTable' => $parentTable,
            'fieldParentTable' => $fieldParentTable
        );
        
        return $this;
    }
    
    public function rightJoin($table, $fieldJoinTable, $parentTable=null, $fieldParentTable=null) {
        if ($fieldParentTable == null)
            $fieldParentTable = $fieldJoinTable;
        if ($parentTable == null)
            $parentTable = $this->table;
        
        $this->rightJoin[] = array(
            'table' => $table,
            'fieldJoinTable' => $fieldJoinTable,
            'parentTable' => $parentTable,
            'fieldParentTable' => $fieldParentTable
        );
        
        return $this;
    }
    
    
    public function addWhere($qbw) {
        $this->whereContainer->addWhere( $qbw );
        
        return $this;
    }
    
    
    public function setOrderBy($o) {
        $this->orderBy = $o;
        return $this;
    }
    public function getOrderBy() { return $this->orderBy; }
    
    public function setGroupBy($g) {
        $this->groupBy = $g;
        return $this;
    }
    public function getGroupBy() { return $this->groupBy; }
    
    
}

