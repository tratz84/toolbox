<?php

namespace core\db;

use core\db\query\QueryBuilderWhere;


class DBObject {
    
    protected $resourceName;
    protected $tableName;
    protected $primaryKey = null;
    
    protected $dbFields = array();
    protected $fields = array();
    
    protected $lastError = null;
    protected $lastQuery = null;
    
    protected $changes = null;
    
    
    public function __construct() {
        
    }
    
    public function setResource($n) { $this->resourceName = $n; }
    
    public function getTableName() { return $this->tableName; }
    public function setTableName($n) { $this->tableName = $n; }

    public function setPrimaryKey($n) { $this->primaryKey = $n; }
    public function getPrimaryKey() { return $this->primaryKey; }
    public function getPrimaryKeyValue() { return $this->getField( $this->primaryKey ); }
    
    protected function setDatabaseFields($arr) { $this->dbFields = $arr; }
    
    public function getColumnType($columnName) {
        if (isset($this->dbFields[$columnName]['Type'])) {
            return $this->dbFields[$columnName]['Type'];
        } else {
            return null;
        }
    }
    
    public function getLastError() { return $this->lastError; }
    public function getLastQuery() { return $this->lastQuery; }
    
    public function trackChanges() { $this->changes = array(); }
    
    public function createQueryBuilder() {
        $qb = DatabaseHandler::getConnection($this->resourceName)->createQueryBuilder();
        $qb->setTable($this->getTableName());
        
        return $qb;
    }
    
    public function isNew() {
        $pk = $this->getField( $this->primaryKey );
        
        
        if ($pk)
            return false;
        else
            return true;
    }
    
    public function hasDatabaseField($fieldname) {
        if (isset($this->dbFields[$fieldname]))
            return true;
        else
            return false;
    }
    
    public function setFields($arr) {
        foreach($arr as $key => $val) {
            $this->setField($key, $val);
        }
    }
    
    public function getFields($fieldList=null) {
        if ($fieldList == null)
            return $this->fields;
        
        $arr = array();
        foreach($fieldList as $key) {
            $arr[$key] = $this->getField($key);
        }
        
        return $arr;
    }
    
    public function hasField($name) {
        if (array_key_exists($name, $this->fields))
            return true;
        else
            return false;
    }
    
    public function setField($name, $val) {
        if (array_key_exists($name, $this->dbFields)) {
            $meta = $this->dbFields[$name];
            
            // cast value
            if ($val === null) {
                
            } else if (strpos($meta['Type'], 'int') === 0) {
                $val = (int)$val;
            }
            else if (strpos($meta['Type'], 'bigint') === 0) {
                $val = (int)$val;
            }
            else if ($meta['Type'] == 'double') {
                $val = (double)$val;
            }
            else if ($meta['Type'] == 'tinyint(1)') {
                $val = (bool)$val ? 1 : 0;
            }
            else if ($meta['Type'] == 'date') {
                $val = valid_date($val) ? $val : null;
            }
        }
        
        if ($this->changes !== null && $val != $this->fields[$name]) {
            $this->changes[$name] = array('old' => $this->fields[$name], 'new' => $val);
        }
        
        $this->fields[$name] = $val;
    }
    public function getField($name, $defaultVal=null) {
        if (array_key_exists($name, $this->fields)) {
            $val = $this->fields[$name];
            
            if (array_key_exists($name, $this->dbFields)) {
                $meta = $this->dbFields[$name];
                
                // cast value
                if ($val === null) {
                    // null is fine :)
                } else if (strpos($meta['Type'], 'int(') === 0) {
                    $val = (int)$val;
                }
                else if (strpos($meta['Type'], 'bigint(') === 0) {
                    $val = (int)$val;
                }
                else if ($meta['Type'] == 'double') {
                    $val = (double)$val;
                }
                else if ($meta['Type'] == 'tinyint(1)') {
                    $val = (bool)$val ? 1 : 0;
                }
                
            }
            
            return $val;
        } else {
            return $defaultVal;
        }
    }
    
    public function read() {
        $pk = $this->getField( $this->primaryKey );
        if (!$pk)
            return false;
        
        $qb = $this->createQueryBuilder();
        $qb->selectFields('*');
        $qb->addWhere(QueryBuilderWhere::whereRefByVal($this->primaryKey, '=', $pk));
        
        $sql = $qb->createSelect();
        $params = $qb->getParams();
        
        return $this->readBy($sql, $params);
    }

    function readBy($query, $params=array()) {
        $c = DatabaseHandler::getConnection($this->resourceName);
        
        $l = $c->queryList($query, $params);
        
        if (count($l)) {
            foreach($l[0] as $key => $val) {
                $this->setField($key, $val);
            }
            
            return true;
        }
        
        return false;
    }
    
    
    public function save() {
        $pk = $this->getField($this->primaryKey);
        
        if (array_key_exists('edited', $this->dbFields))
            $this->setField('edited', date('Y-m-d H:i:s'));

        if ($pk) {
            return $this->update();
        } else {
            if (array_key_exists('created', $this->dbFields))
                $this->setField('created', date('Y-m-d H:i:s'));
            
            return $this->insert();
        }
        
    }
    
    public function insert() {
        $qb = $this->createQueryBuilder();
        
        foreach($this->dbFields as $f => $fieldSettings) {
            // don't insert PK
            if ($f == $this->primaryKey)
                continue;
            
            $value = $this->getField($f);
            
            $qb->setFieldValue($f, $value);
        }
        
        $res = $qb->queryInsert();
        
        $this->lastQuery = $qb->getConnection()->getLastQuery();
        
        $insert_id = $qb->getConnection()->getInsertId();
        
        if ($insert_id) {
            $this->setField($this->primaryKey, $insert_id);
            return true;
        } else {
            $this->lastError = $res->error;
            
            return false;
        }
    }
    
    public function update() {
        $qb = $this->createQueryBuilder();
        
        foreach($this->dbFields as $f => $fieldSettings) {
            // don't update PK
            if ($f == $this->primaryKey)
                continue;
            
            $value = $this->getField($f);
            
            $qb->setFieldValue($f, $value);
        }
        
        $qb->addWhere(QueryBuilderWhere::whereRefByVal($this->primaryKey, '=', $this->getField($this->primaryKey)));
        
        $result = $qb->queryUpdate();
        $this->lastQuery = DatabaseHandler::getInstance()->getLastQuery();
        
        if ($result) {
            return true;
        }
        
        $res = DatabaseHandler::getInstance()->getResource($this->resourceName);
        
        $this->lastError = $res->error;
        
        return false;
    }
    
    
    public function delete() {
        $pk = $this->getField( $this->primaryKey );
        
        if (!$pk)
            return false;
        
        $qb = $this->createQueryBuilder();
        $qb->addWhere(QueryBuilderWhere::whereRefByVal($this->primaryKey, '=', $pk));
        $qb->queryDelete();
        
        $this->lastQuery = DatabaseHandler::getInstance()->getLastQuery();
        
        // TODO: check affected rows
        
        return true;
    }
    
    
    public function __set($key, $val) {
        $func = 'set'.dbCamelCase($key);
        
        if (is_callable(array($this, $func))) {
            $this->$func( $val );
        } else {
            $this->setField($key, $val);
        }
    }
    
    public function __get($key) {
        $func = 'get'.dbCamelCase($key);
        
        if (is_callable(array($this, $func))) {
            return $this->$func( );
        } else {
            return $this->getField( $key );
        }
    }
    
    
    
}

