<?php

namespace core\container;


use core\db\DBObject;
use core\exception\InvalidStateException;

class ArrayContainer {
    
    protected $items;
    
    protected $attributes = array();
    
    
    public function __construct($items=array()) {
        $this->items = $items;
    }
    
    
    public function setAttribute($name, $value) { $this->attributes[$name] = $value; }
    public function getAttribute($name, $defaultValue=null) {
        if (isset($this->attributes[$name])) {
            return $this->attributes[$name];
        } else {
            return $defaultValue;
        }
    }
    
    
    public function getItems() { return $this->items; }
    public function setItems($i) { $this->items = $i; }
    
    public function count() { return count($this->items); }
    
    public function clear() { $this->items = array(); }
    
    public function add($i) { $this->items[] = $i; }
    public function get($i) {
        if (isset($this->items[$i])) {
            return $this->items[$i];
        } else {
            throw new InvalidStateException('Invalid index');
        }
    }
    
    public function set($pos, $item) {
        $this->items[$pos] = $item;
    }
    
    public function insert($item, $no=0) {
        if (is_int($no) == false || $no < 0)
            return false;
        
        if ($no >= $this->count()) {
            $this->items[] = $item;
            return true;
        }
        
        $newList = array();
        for($x=0; $x < count($this->items); $x++) {
            if ($x == $no) {
                $newList[] = $item;
            }
            
            $newList[] = $this->items[$x];
        }
        
        $this->items = $newList;
        
        return true;
    }
    
    public function removeNo($no) {
        if (is_int($no) == false || $no < 0 || $no >= $this->count())
            return false;
        
        $newList = array();
        for($x=0; $x < count($this->items); $x++) {
            if ($x == $no)
                continue;
            
            $newList[] = $this->items[$x];
        }
        
        $this->items = $newList;
        
        return true;
    }
    
    public function sort($field='sort') {
        usort($this->items, function($i1, $i2) use ($field) {
            $v1 = $i1;
            $v2 = $i2;
            
            
            if (is_object($i1)) {
                $method = 'get'.ucfirst($field);
                if (is_a($i1, DBObject::class)) {
                    if (method_exists($i1, $method)) {
                        $v1 = $i1->{$method}();
                    } else {
                        $v1 = $i1->getField($field);
                    }
                }
            } else if (is_array($i1)) {
                $v1 = $i1[$field];
            }
            
            if (is_object($i2)) {
                $method = 'get'.ucfirst($field);
                if (is_a($i2, DBObject::class)) {
                    if (method_exists($i2, $method)) {
                        $v2 = $i2->{$method}();
                    } else {
                        $v2 = $i2->getField($field);
                    }
                }
            } else if (is_array($i2)) {
                $v2 = $i2[$field];
            }
            
            if (is_numeric($v1) && is_numeric($v2)) {
                return $v1 - $v2;
            } else {
                strcmp($v1, $v2);
            }
        });
            
    }
    
}
