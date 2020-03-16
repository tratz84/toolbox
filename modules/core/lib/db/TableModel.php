<?php 


namespace core\db;

use core\exception\InvalidStateException;

class TableModel {
    
    protected $data = array();
    
    public function __construct($schemaName, $tableName) {
        $this->setSchemaName($schemaName);
        $this->setTableName($tableName);
        
        $this->data['columns'] = array();
        $this->data['uniqueIndexes'] = array();
        $this->data['indexes'] = array();
    }
    
    public function setSchemaName($n) { $this->data['schemaName'] = $n; }
    public function getSchemaName() { return $this->data['schemaName']; }
    
    public function setTableName($n) { $this->data['tableName'] = $n; }
    public function getTableName() { return $this->data['tableName']; }
    
    public function getColumns() {
        return array_keys( $this->data['columns'] );
    }
    
    public function addColumn($columnName, $type, $props=array()) {
        $this->data['columns'][$columnName] = array(
            'type' => $type
        );
        
        $this->data['columns'][$columnName] = array_merge($this->data['columns'][$columnName], $props);
    }
    
    
    public function setPrimaryKey($columnName, $autoIncrement=true) {
        $this->setColumnProperty($columnName, 'key', 'PRIMARY KEY');
        $this->setColumnProperty($columnName, 'auto_increment', true);
    }
    
    public function setColumnProperty($columnName, $propName, $propVal) {
        if (isset($this->data['columns'][$columnName]) == false) {
            throw new InvalidStateException('Unknown column');
        }
        
        $this->data['columns'][$columnName][$propName] = $propVal;
    }
    public function getColumnProperty($columnName, $propertyName, $defaultValue=null) {
        if (isset($this->data['columns'][$columnName]) == false) {
            throw new InvalidStateException('Unknown column');
        }
        
        if (isset($this->data['columns'][$columnName][$propertyName])) {
            return $this->data['columns'][$columnName][$propertyName];
        } else {
            return $defaultValue;
        }
    }
    
    public function hasColumn($columnName) { return isset($this->data['columns'][$columnName]) ? true : false; }
    public function getColumn($columnName) {
        if (isset($this->data['columns'][$columnName]) == false) {
            throw new InvalidStateException('Unknown column');
        }
        
        return $this->data['columns'][$columnName];
    }

    
    public function hasIndex($indexName) { return isset($this->data['indexes'][$indexName]) ? true : false; }
    public function getIndexes() { return $this->data['indexes']; }
    public function getIndex($indexName) { return $this->data['indexes'][$indexName]; }
    public function setIndex($indexName, $columns=array()) { $this->data['indexes'][$indexName] = $columns; }
    
    
    public function hasUniqueConstraint($indexName) { return isset($this->data['uniqueColumns'][$indexName]) ? true : false; }
    public function getUniqueConstraints() { return $this->data['uniqueColumns']; }
    public function getUniqueConstraint($indexName) { return $this->data['uniqueColumns'][$indexName]; }
    public function setUniqueColumns($indexName, $columns=array()) { $this->data['uniqueColumns'][$indexName] = $columns; }
    
}

