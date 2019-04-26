<?php

namespace core\db;


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
    
    protected function setDatabaseFields($arr) { $this->dbFields = $arr; }
    
    public function getLastError() { return $this->lastError; }
    public function getLastQuery() { return $this->lastQuery; }
    
    public function trackChanges() { $this->changes = array(); }
    
    
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
        
        return $this->readBy('select * from '.$this->tableName.' where '.$this->primaryKey . ' = ?', array($pk));
    }

    function readBy($query, $params=array()) {
        $r = query($this->resourceName, $query, $params);
        
        if ($r && ($row = $r->fetch_assoc())) {
            foreach($row as $key => $val) {
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
        $insertFields = array();
        $insertMarks = array();
        $params = array();
        
        foreach($this->dbFields as $f => $fieldSettings) {
            // don't insert PK
            if ($f == $this->primaryKey)
                continue;
            
            $value = $this->getField($f);
            $insertFields[] = '`'.$f.'`';
            
            if ($value === null) {
                $insertMarks[] = 'NULL';
            } else {
                $insertMarks[] = '?';
                $params[] = $value;
            }
        }
        
        $sql = "insert into ".$this->tableName." (".implode(', ', $insertFields).") VALUES (".implode(', ', $insertMarks).")";
        
        $this->lastQuery = $sql;
        query($this->resourceName, $sql, $params);
        
        $res = DatabaseHandler::getInstance()->getResource($this->resourceName);
        $insert_id = $res->insert_id;
        
        if ($insert_id) {
            $this->setField($this->primaryKey, $insert_id);
            return true;
        } else {
            $this->lastError = $res->error;
            
            return false;
        }
    }
    
    public function update() {
        $updateFields = array();
        $params = array();
        
        foreach($this->dbFields as $f => $fieldSettings) {
            // don't update PK
            if ($f == $this->primaryKey)
                continue;
            
            $value = $this->getField($f);
            if ($value === null) {
                $updateFields[] = '`'.$f.'`'.' = NULL ';
            } else {
                $updateFields[] = '`'.$f.'`'.' = ? ';
                $params[] = $value;
            }
        }
        
        $sql = "update ".$this->tableName." SET ".implode(', ', $updateFields)." WHERE ".$this->primaryKey." = ?";
        $params[] = $this->getField($this->primaryKey);
        
        $this->lastQuery = $sql;
        $result = query($this->resourceName, $sql, $params);
        
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
        
        $sql = "delete from ".$this->tableName." where ".$this->primaryKey . " = ?";
        
        $this->lastQuery = $sql;
        $r = query($this->resourceName, $sql, array($pk));
        
        // TODO: check affected rows
        
        return true;
    }
    
    
}

