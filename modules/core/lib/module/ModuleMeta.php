<?php


namespace core\module;


class ModuleMeta {
    
    protected $tag;
    protected $name;
    protected $infoText;
    protected $prio = 0;
    
    protected $dependentModules = array();
    protected $props = array();
    
    public function __construct($tag, $name, $infoText, $prio=999) {
        $this->setTag($tag);
        $this->setName($name);
        $this->setInfoText($infoText);
        $this->setPrio( $prio );
    }
    
    
    public function setTag($p) { $this->tag = $p; }
    public function getTag() { return $this->tag; }
    
    public function setName($p) { $this->name = $p; }
    public function getName() { return $this->name; }
    
    public function setInfoText($p) { $this->infoText = $p; }
    public function getInfoText() { return $this->infoText; }
    
    
    public function setPrio($p) { $this->prio = $p; }
    public function getPrio() { return $this->prio; }
    
    
    public function addDependency($n) { $this->dependentModules[] = $n; }
    public function getDependencies() { return $this->dependentModules; }
    
    
    public function setProperty($key, $val) { $this->props[$key] = $val; }
    public function getProperty($key, $defaultValue=null) {
        if (isset($this->props[$key])) {
            return $this->props[$key];
        } else {
            return $defaultValue;
        }
    }
    
}

