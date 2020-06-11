<?php

namespace core\db;

use core\db\query\QueryBuilder;

class DAOObject
{

    protected $resourceName;
    protected $objectName;

    public function setResource($name) { $this->resourceName = $name; }
    
    public function getObjectName() { return $this->objectName; }
    public function setObjectName($name) { $this->objectName = $name; }

    /**
     * @return QueryBuilder
     */
    protected function createQueryBuilder() {
        $qb = DatabaseHandler::getConnection($this->resourceName)->createQueryBuilder();
        $qb->setObjectName( $this->objectName );
        return $qb;
    }
    
    protected function query($query, $params=array()) {
        $con = DatabaseHandler::getConnection($this->resourceName);
        
        return $con->query($query, $params);
    }
    
    
    public function queryList($query, $params = array()) {
        $con = DatabaseHandler::getConnection($this->resourceName);
        $list = array();
        
        $rows = $con->queryList($query, $params);
        foreach($rows as $r) {
            $obj = new $this->objectName();
            $obj->setFields($r);
            
            $list[] = $obj;
        }
        
        return $list;
    }
    
    public function escape($str) {
        $con = DatabaseHandler::getConnection($this->resourceName);
        return $con->escape($str);
    }
    
    

    /**
     * queryOne() - return's first result or NULL
     * @param unknown $query
     * @param array $params
     * @return unknown|NULL
     */
    public function queryOne($query, $params = array()) {
        $con = DatabaseHandler::getConnection($this->resourceName);
        $res = $con->query($query, $params);
        
        $row = $res->fetch_assoc();
        if ($row) {
            $obj = new $this->objectName();
            $obj->setFields($row);
            
            return $obj;
        }
        
        return null;
    }
    
    protected function queryValue($query, $params=array()) {
        $con = DatabaseHandler::getConnection($this->resourceName);
        
        $r = $con->queryOne($query, $params);
        
        if ($r && count($r)) {
            $keys = array_keys($r);
            
            return $r[ $keys[0] ];
        } else {
            return null;
        }
    }
    
    
    
    public function queryCursor($query, $params = array()) {
        $con = DatabaseHandler::getConnection($this->resourceName);
        
        $cursor = $con->queryCursor($this->objectName, $query, $params);
        
        return $cursor;
    }
    
    
    public function mergeFormListMTO1($linkKey, $linkKeyValue, &$newList) {
        
        // get tablename for object
        $obj = new $this->objectName();
        $pk = $obj->getPrimaryKey();
        $tbl = $obj->getTableName();
        
        // list of id's
        $currentIds = array();
        
        // save list
        $sort=0;
        foreach($newList as &$arr) {
            // fetch object
            if (is_a($arr, \core\db\DBObject::class)) {
                $obj = $arr;
            } else {
                $obj = new $this->objectName();
            
                if (isset($arr[$obj->getPrimaryKey()]) && $arr[$obj->getPrimaryKey()]) {
                    $obj->setField($obj->getPrimaryKey(), $arr[$obj->getPrimaryKey()]);
                    $obj->read();
                }
            }
            
            
            // if it ain't there it will be ignored
            $obj->setField('sort', $sort++);
            
            // set formdata
            $obj->setFields($arr);

            // set link key
            $obj->setField($linkKey, $linkKeyValue);
            
            // save object new list
            if ($obj->save()) {
                $pkValue = $obj->getField( $obj->getPrimaryKey() );
                $currentIds[] = $pkValue;
                
                if (is_a($arr, \core\db\DBObject::class)) {
                    $arr->setField($obj->getPrimaryKey(), $pkValue);
                } else {
                    $arr[$obj->getPrimaryKey()] = $pkValue;
                }
            }
        }
        
        $sql = "delete from `".$tbl."` where `".$linkKey."` = ? ";
        if (count($currentIds)) {
            $sql .= " AND `".$pk."` NOT IN (" . implode(', ', $currentIds) . ") ";
        }
        
        $this->query($sql, array($linkKeyValue));
    }
    
    
    public function mergeFormListMTON($linkTable, $linkKey, $linkKeyValue, $newList, $sortfield=null) {
        
        $newIds = array();
        
        $cnt=0;
        foreach($newList as $arr) {
            // fetch object
            $obj = new $this->objectName();
            if ($arr[$obj->getPrimaryKey()]) {
                $obj->setField($obj->getPrimaryKey(), $arr[$obj->getPrimaryKey()]);
                $obj->read();
            }
            
            // set formdata
            $obj->setFields($arr);
            
            // save object new list
            if ($obj->save()) {
                $objPK = $obj->getField($obj->getPrimaryKey());
                $newIds[] = $objPK;
                
                // add link
                $sql = "select * from ".$linkTable . " where $linkKey = ? and ".$obj->getPrimaryKey()." = ?";
                $res = $this->query($sql, array($linkKeyValue, $objPK));
                if ($row = mysqli_fetch_assoc($res)) {
                    // already linked, maybe update sort-field
                    
                    if ($sortfield) {
                        $keys = array_keys($row);
                        $pkLinktable = $keys[0];
                        $sql = "update $linkTable set $sortfield = ? where  `" . $pkLinktable . "` = ?";
                        $this->query($sql, array($cnt, $row[$pkLinktable]));
                    }
                } else {
                    if ($sortfield) {
                        $sql = "insert into $linkTable ( $linkKey, ".$obj->getPrimaryKey().", ".$sortfield.") VALUES (?, ?, ?)";
                        $this->query($sql, array($linkKeyValue, $objPK, $cnt));
                    } else {
                        $sql = "insert into $linkTable ( $linkKey, ".$obj->getPrimaryKey().") VALUES (?, ?)";
                        $this->query($sql, array($linkKeyValue, $objPK));
                    }
                }
            }
            
            $cnt++;
        }
        
        $obj = new $this->objectName();
        
        $sql = "delete from ".$obj->getTableName()." where ".$obj->getPrimaryKey()." in (select ".$obj->getPrimaryKey()." from ".$linkTable." where $linkKey = ?) ";
        if (count($newIds))
            $sql .= " and ".$obj->getPrimaryKey()." not in (".implode(',', $newIds).") ";
        
        
        $this->query($sql, array($linkKeyValue));
        
    }
    
    
    public function mergeLists($uniqueField, $currentObjects, $newEntriesArray) {
        
        $newList = array();
        $toBeDeleted = array();
        
        foreach ($newEntriesArray as $arr) {
            $tmp = new $this->objectName();
            
            $tmp->setField($tmp->getPrimaryKey(), $arr[$tmp->getPrimaryKey()]);
            $tmp->read();
            $tmp->setFields($arr);
            
            $newList[] = $tmp;
        }
        
        foreach($currentObjects as $curObj) {
            $id = $curObj->getField($uniqueField);
            
            $blnFound = false;
            foreach($newList as $n) {
                if ($id == $n->getField($uniqueField)) {
                    $blnFound = true;
                    break;
                }
            }
            
            if ($blnFound == false) {
                $toBeDeleted[] = $curObj;
            }
        }
        
        
        
        return $newList;
    }
    
    
    /**
     * deleteMTON() - delete M-TO-N records in both link table as end table
     * 
     * Usage: $dao = new AddressDAO(); $dao->deleteMTON('customer__person_address', 'person_id', $personId)
     * 
     */
    public function deleteMTON($linkTable, $linkKey, $linkValue) {
        
        $linkList = array();
        $res = $this->query('select * from `'.$linkTable.'` where `'.$linkKey.'` = ?', array($linkValue));
        while($row = $res->fetch_assoc()) {
            $linkList[] = $row;
        }
        
        // delete data in link table
        $this->query('delete from `'.$linkTable.'` where `'.$linkKey.'` = ?', array($linkValue));
        
        
        $obj = new $this->objectName();
        $table = $obj->getTableName();
        $pk = $obj->getPrimaryKey();
        foreach($linkList as $l) {
            $this->query('delete from `'.$table.'` where `'.$pk.'` = ?', array($l[$pk]));
        }
        
    }
    
    
    public function updateSort($ids, $field='sort') {
        if (is_array($ids) == false || count($ids) == 0)
            return -1;
        
        $obj = new $this->objectName();
        
        $table = $obj->getTableName();
        $pk = $obj->getPrimaryKey();
        
        $updated = 0;
        for($x=0; $x < count($ids); $x++) {
            $i = (int)$ids[$x];
            if ($i) {
                $this->query("update `".$table."` set `".$field."` = ? where `".$pk."` = ?", array($x, $i));
                $updated++;
            }
        }
        
        return $updated;
    }
    
    public function unsetDefaultSelected($except_id) {
        $obj = new $this->objectName();
        $tableName = $obj->getTableName();
        $pk = $obj->getPrimaryKey();
        
        $this->query("update `".$tableName."` set default_selected = false where `".$pk."` <> ?", array($except_id));
    }
    
    
    
    
    public function updateField($id, $field, $value) {
        $obj = new $this->objectName();
        $tableName = $obj->getTableName();
        $pk = $obj->getPrimaryKey();
        
        // validate field (prevent sql injection..)
        if (!$obj->hasDatabaseField($field))
            return false;
            
        if ($value === null) {
            return $this->query("update `{$tableName}` set `{$field}` = NULL where `{$pk}` = ?", array($id));
        } else {
            return $this->query("update `{$tableName}` set `{$field}` = ? where `{$pk}` = ?", array($value, $id));
        }
    }
    
}

