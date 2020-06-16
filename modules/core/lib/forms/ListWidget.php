<?php


namespace core\forms;

use core\db\DBObject;


class ListWidget extends WidgetContainer {
    
    /**
     * name of get/set method for this list on DBObject, ie 'offerLines', for Offer->getOfferLines()
     */
    protected $methodObjectList;
    
    
    
    public function setMethodObjectList($mol) { $this->methodObjectList = $mol; }
    public function getMethodObjectList() { return $this->methodObjectList; }
    
    public function asObjects(string $className) {
        $arr = $this->asArray();
        
        $l = array();
        
        foreach($arr as $a) {
            $o = new $className();
            $o->setFields($a);
            
            $l[] = $o;
        }
        
        return $l;
    }
    
    protected function retrieveObjects($obj) {
        $get_func = 'get'.ucfirst($this->methodObjectList);
        
        $objects = array();
        
        if (method_exists($obj, $get_func)) {
            
            $objects = $obj->$get_func();
        } else if (isset($obj->{$this->methodObjectList})) {
            $objects = $obj->{$this->methodObjectList};
        } else if (is_array($obj) && isset($obj[$this->methodObjectList])) {
            $objects = $obj[$this->methodObjectList];
        } else {
            // might happen if field list-field is empty
            //             throw new InvalidStateException('methodObjectList not found on object - ' . $this->methodObjectList);
        }
        
        // empty?
        if ($objects == null) {
            $objects = array();
        }
        
        return $objects;
    }
    
}

