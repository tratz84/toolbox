<?php


namespace core\container;


use core\exception\MethodNotFoundException;
use core\ObjectContainer;
use core\event\EventBus;

class ObjectHookProxy {
    
    protected $obj;
    
    public function __construct($obj) {
        $this->obj = $obj;
    }
    
    
    public function __call($name, $arguments) {
        if (method_exists($this->obj, $name) == false) {
            throw new MethodNotFoundException('method not found: ' . get_class($this->obj) . '::' . $name);
        }
        
        $eb = ObjectContainer::getInstance()->get(EventBus::class);
        
        $r = call_user_func_array(array($this->obj, $name), $arguments);
        
        $eb->publishEvent(array($r, $arguments), 'core', 'object-hook-'.get_class($this->obj).'::'.$name);
        
        return $r;
    }
    
}

