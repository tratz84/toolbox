<?php

namespace core\db\query;

use core\db\Cursor;


class MysqlCursor extends Cursor {
    
    protected $mysqliResult;
    protected $numRows;
    
    protected $nextObject;
    protected $currentObject;
    
    function __construct($objectName, $mysqli_result) {
        parent::__construct($objectName);
        
        $this->mysqliResult = $mysqli_result;
        
        $this->numRows = $mysqli_result->num_rows;
        $this->nextObject = null;
        
        $this->currentObject = null;
    }
    
    
    function numRows() { return $this->numRows; }
    function current() { return $this->currentObject; }
    
    function hasNext() {
        if ($this->nextObject !== null) {	// reeds een volgend object in cache?
            return true;
        } else {
            // volgend object aanvragen
            $o = $this->next();
            
            if ($o) {						// resultaat? => volgend object beschikbaar!
                $this->nextObject = $o;
                return true;
            } else {						// geen resultaat => geen volgend object
                return false;
            }
        }
    }
    
    function moveTo($no) {
        if ($this->numRows > 0) {
            $this->nextObject = null;
            return $this->mysqliResult->data_seek( $no );
        }
    }
    
    function next() {
        if ($this->nextObject !== null) {			// hasNext() aangeroepen? => mNextObject teruggeven!
            $o = $this->nextObject;
            $this->nextObject = null;
            
            $this->currentObject = $o;
            return $o;
        }
        
        // volgend object opvragen
        $row = $this->mysqliResult->fetch_assoc();
        if (!$row)
            return null;
            
            // class instantieren en teruggeven
            $o = new $this->objectName();
            $o->setFields( $row );
            
            $this->currentObject = $o;
            return $o;
    }
    
    
    public function free() {
        if ($this->mysqliResult) {
            $this->mysqliResult->free();
            $this->mysqliResult = null;
        }
    }
}