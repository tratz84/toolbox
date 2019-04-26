<?php


namespace webmail\model;


class Filter extends base\FilterBase {

    protected $conditions;
    protected $actions;
    
    public function __construct($id=null) {
        parent::__construct($id);
        
        $this->setActive(true);
    }
    
    
    public function getConditions() { return $this->conditions; }
    public function setConditions($c) { $this->conditions = $c; }
    
    public function getActions() { return $this->actions ; }
    public function setActions($a) { $this->actions = $a; }
    

}

