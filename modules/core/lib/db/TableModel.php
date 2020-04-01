<?php 


namespace core\db;

use core\exception\InvalidStateException;
use core\exception\DebugException;

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
        $this->data['foreignKeys'] = array();
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
        // throw an error, if a space is accidentally added it causes the MysqlTableGenerator to create a drop-column & add-column statement
        // it's probably not necessary to throw an error here, because mysql doesn't allow columns with spaces
        if ($columnName != trim($columnName)) {
            throw new DebugException('Column name contains spaces before/after name');
        }
        
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
    
    
    public function getForeignKeys() {
        if (isset($this->data['foreignKeys'])) {
            return $this->data['foreignKeys'];
        } else {
            return array();
        }
    }
    
    public function addForeignKey($indexName, $columns, $refTable, $refColumns, $onDelete=null, $onUpdate=null) {
        
        if (is_string($columns)) {
            $columns = array( $columns );
        }
        
        // TODO: auto add index for foreign keys?
//         $this->addIndex($indexName, $columns);
            
        
        if (is_string($refColumns)) {
            $refColumns = array( $refColumns );
        }
        
        if ($onDelete == null) $onDelete = 'set default';
        if ($onUpdate == null) $onUpdate = 'set default';
        
        $onDelete = strtoupper(trim($onDelete));
        $onUpdate = strtoupper(trim($onUpdate));
        
        $ref_opts = array('RESTRICT', 'CASCADE', 'SET NULL', 'NO ACTION', 'SET DEFAULT');
        
        if (in_array($onDelete, $ref_opts) == false) {
            throw new \core\exception\InvalidArgumentException('Invalid onDelete value');
        }
        if (in_array($onUpdate, $ref_opts) == false) {
            throw new \core\exception\InvalidArgumentException('Invalid onUpdate value');
        }
        
        
        
        $this->data['foreignKeys'][$indexName] = array(
            'columns' => $columns,
            'ref_table' => $refTable,
            'ref_columns' => $refColumns,
            'on_delete' => $onDelete,
            'on_update' => $onUpdate
        );
        
    }
    
    
}

