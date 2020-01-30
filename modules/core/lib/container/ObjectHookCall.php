<?php


namespace core\container;


use core\exception\InvalidStateException;

class ObjectHookCall {
    
    protected $object;
    protected $functionName;
    protected $arguments;
    protected $returnValue;
    
    
    public function __construct($object, $functionName=null, $arguments=array(), $returnValue=null) {
        $this->object = $object;
        $this->functionName = $functionName;
        $this->arguments = $arguments;
        $this->returnValue = $returnValue;
    }
    
    public function getObject() { return $this->object; }
    public function setObject($o) { $this->object = $o; }
    
    public function getFunctionName() { return $this->functionName; }
    public function setFunctionName($n) { $this->functionName = $n; }
    
    public function getArguments() { return $this->arguments; }
    public function setArguments($args) { $this->arguments = $args; }
    
    public function getArgumentNo($no) {
        if ($no >= count($this->arguments) || $no < 0) {
            throw new InvalidStateException('Invalid argument no');
        }
        
        return $this->arguments[$no];
    }
    
    public function getReturnValue() { return $this->returnValue; }
    public function setReturnValue($v) { $this->returnValue = $v; }
    
    
}
