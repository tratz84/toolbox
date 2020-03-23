<?php 


namespace core\db;

use core\exception\InvalidStateException;

class TableModel {
    
    protected $data = array();
    
    /**
     * 
     * @param string $schemaName
     * @param string $tableName
     * @param string $resourceName - database resource name (default / admin)
     */
    public function __construct($schemaName, $tableName, $resourceName='default') {
        $this->setResourceName($resourceName);
        $this->setSchemaName($schemaName);
        $this->setTableName($tableName);
        
        $this->data['schemaInTableName'] = true;
        
        $this->data['columns'] = array();
        $this->data['indexes'] = array();
    }
    
    
    public function getData() { return $this->data; }
    
    
    public function setUseSchemaInTableName($bln) { $this->data['schemaInTableName'] = $bln ? true : false; }
    public function useSchemaInTableName() { return $this->data['schemaInTableName'] ? true : false; }

    public function setResourceName($n) { $this->data['resourceName'] = $n; }
    public function getResourceName() { return $this->data['resourceName']; }
    
    public function setSchemaName($n) { $this->data['schemaName'] = $n; }
    public function getSchemaName() { return $this->data['schemaName']; }
    
    public function setTableName($n) { $this->data['tableName'] = $n; }
    public function getTableName() { return $this->data['tableName']; }
    
    public function getColumns() {
        return array_keys( $this->data['columns'] );
    }
    
    public function getPrimaryKeys() {
        $keys = array();
        
        foreach($this->data['columns'] as $c) {
            if (isset($c['key']) && $c['key'] == 'PRIMARY KEY') {
                $keys[] = $c['name'];
            }
        }
        
        return $keys;
    }
    
    
    public function addColumn($columnName, $type, $props=array()) {
        $coldata = array(
            'type' => $type,
            'name' => $columnName,
        );
        
        $this->data['columns'][$columnName] = array_merge($coldata, $props);
    }
    
    
    public function setPrimaryKey($columnName, $autoIncrement=true) {
        if (is_array($columnName)) {
            $pks = $columnName;
        } else {
            $pks = array( $columnName );
        }
        
        foreach($pks as $pk) {
            $this->setColumnProperty($pk, 'key', 'PRIMARY KEY');
            
            if ($autoIncrement) {
                $this->setColumnProperty($pk, 'auto_increment', true);
            }
        }
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
    public function getIndex($indexName) {
        return $this->data['indexes'][$indexName];
    }
    public function addIndex($indexName, $columns=array(), $props=array()) {
        $data = array(
            'columns' => $columns,
            'name' => $indexName,
        );
        
        $this->data['indexes'][$indexName] = array_merge($data, $props);
    }
    
    
}

