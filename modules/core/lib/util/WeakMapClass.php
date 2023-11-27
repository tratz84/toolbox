<?php


namespace core\util;

class WeakMapClass
{

    protected $classProps = [];

    public function &__get($name) {
        return $this->classProps[$name];
    }

    public function __isset($name) {
        return isset($this->classProps[$name]);
    }

    public function __set($name, $value) {
        $this->classProps[$name] = $value;
    }

    public function __unset($name) {
        unset($this->classProps[$name]);
    }
    
    public function getObjectVars() {
        $vars1 = get_object_vars( $this );
        $vars2 = $this->classProps;
        
        return array_merge( $vars1, $vars2 );
    }
    
}


