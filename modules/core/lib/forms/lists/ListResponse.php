<?php

namespace core\forms\lists;

use core\exception\InvalidArgumentException;

class ListResponse {
    
    public $rowCount;
    public $pageSize;
    public $start;
    public $objects;
    
    public function __construct($start, $pageSize, $rowCount, $objects) {
        $this->setStart($start);
        $this->setPageSize($pageSize);
        $this->setRowCount($rowCount);
        $this->setObjects($objects);
    }

    public static function fillByDBObjects($start, $pageSize, $dbobjects, $fields=array()) {
        
        // no pagesize given? => use all objects
        if ($pageSize == null) {
            $pageSize = count($dbobjects);
        }
        
        
        $objects = array();
        
        for($x=0; $x < $pageSize && $x < count($dbobjects); $x++) {
            
            $row = $dbobjects[$x];
            
            $obj = array();
            foreach($fields as $f) {
                
                $func = 'get'.dbCamelCase($f);
                if (method_exists($row, $func)) {
                    $val = $row->$func();
                    $obj[$f] = $val;
                } else {
                    $val = $row->getField($f);
                    $obj[$f] = $val;
                }
            }
            
            $objects[] = $obj;
        }
        
        $r = new ListResponse($start, $pageSize, count($dbobjects), $objects);
        
        return $r;
        
    }
    
    public static function fillByCursor($start, $pageSize, $cursor, $fields=array()) {
        $rowCount = $cursor->numRows();
        
        $objects = array();
        
        if ($start == 0 || $cursor->moveTo($start)) {
            
            for($x=0; $x < $pageSize; $x++) {
                $row = $cursor->next();
                
                if (!$row)
                    break;
                
                $obj = array();
                foreach($fields as $f) {
                    
                    $func = 'get'.dbCamelCase($f);
                    if (method_exists($row, $func)) {
                        $val = $row->$func();
                        $obj[$f] = $val;
                    } else {
                        $val = $row->getField($f);
                        $obj[$f] = $val;
                    }
                }
                
                $objects[] = $obj;
            }
        }
        
        $r = new ListResponse($start, $pageSize, $rowCount, $objects);
        
        return $r;
    }
    
    /**
     * getRowCount() - total number of records in query response
     */
    public function getRowCount() { return $this->rowCount; }
    public function setRowCount($p) { $this->rowCount = $p; }
    
    /**
     * getPageSize() - paging size
     */
    public function getPageSize() { return $this->pageSize; }
    public function setPageSize($p) { $this->pageSize = $p; }
    
    public function getStart() { return $this->start; }
    public function setStart($p) { $this->start = $p; }
    
    /**
     * getObjectCount() - number of objects in current response
     */
    public function getObjectCount() { return count($this->objects); }
    public function getObjects() { return $this->objects; }
    public function setObjects($p) { $this->objects = $p; }
    
    public function getObject($no) {
        if (is_array($this->objects) && $no >= 0 && $no < count($this->objects)) {
            return $this->objects[$no];
        }
        
        throw new InvalidArgumentException('Invalid object-no');
    }
    
}
