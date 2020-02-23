<?php 


namespace core\db;

use core\exception\InvalidStateException;

class TableModel {
    
    protected $schemaName;
    protected $tableName;
    
    protected $columns = array();
    protected $uniqueColumns = array();
    
    public function __construct($schemaName, $tableName) {
        $this->setSchemaName($schemaName);
        $this->setTableName($tableName);
        
    }
    
    public function setSchemaName($n) { $this->schemaName = $n; }
    public function getSchemaName() { return $this->schemaName; }
    
    public function setTableName($n) { $this->tableName = $n; }
    public function getTableName() { return $this->tableName; }
    
    public function getColumns() {
        return array_keys( $this->columns );
    }
    
    public function addColumn($columnName, $type, $props=array()) {
        $this->columns[$columnName] = array(
            'type' => $type
        );
        
        $this->columns[$columnName] = array_merge($this->columns[$columnName], $props);
    }
    
    
    public function setPrimaryKey($columnName, $autoIncrement=true) {
        $this->setColumnProperty($columnName, 'key', 'PRIMARY KEY');
        $this->setColumnProperty($columnName, 'auto_increment', true);
    }
    
    public function setColumnProperty($columnName, $propName, $propVal) {
        if (isset($this->columns[$columnName]) == false) {
            throw new InvalidStateException('Unknown column');
        }
        
        $this->columns[$columnName][$propName] = $propVal;
    }
    public function getColumnProperty($columnName, $propertyName, $defaultValue=null) {
        if (isset($this->columns[$columnName]) == false) {
            throw new InvalidStateException('Unknown column');
        }
        
        if (isset($this->columns[$columnName][$propertyName])) {
            return $this->columns[$columnName][$propertyName];
        } else {
            return $defaultValue;
        }
    }
    
    public function getColumn($columnName) {
        if (isset($this->columns[$columnName]) == false) {
            throw new InvalidStateException('Unknown column');
        }
        
        return $this->columns[$columnName];
    }
    
    
    public function unsetUniqueColumns($indexName) {
        unset($this->uniqueColumns[$indexName]);
    }

    public function setUniqueColumns($indexName, $columns) {
        $this->uniqueColumns[$indexName] = $columns;
    }
    
}

