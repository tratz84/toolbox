<?php

namespace core\event;

use ReflectionFunction;


class VariableFilter {
    
    protected static $instance = null;
    
    protected $filters = array();
    protected $filtersSorted = array();
    
    
    public function addFilter($filterName, $callback, $prio) {
        $this->filters[$filterName][] = array(
            'callback' => $callback,
            'prio' => $prio
        );
        
        // sort next applyFilter() call
        $this->filtersSorted[$filterName] = false;
    }
    
    public function applyFilter($filterName, $value, $opts=array()) {
        // no filter set? => return value
        if (isset($this->filters[$filterName]) == false) {
            return $value;
        }
        
        // sort?
        if ($this->filtersSorted[$filterName] == false) {
            usort($this->filters[$filterName], function($o1, $o2) {
                return $o1['prio'] - $o2['prio'];
            });
            
            $this->filtersSorted[$filterName] = true;
        }
        
        foreach($this->filters[$filterName] as $filter) {
            $callback = $filter['callback'];
            
            $ref = new ReflectionFunction( $callback );
            if ($ref->getNumberOfParameters() == 2)
                $value = $callback( $value, $opts );
            else
                $value = $callback( $value );
        }
        
        return $value;
    }
    
    
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new VariableFilter();
        }
        
        return self::$instance;
    }
    
}