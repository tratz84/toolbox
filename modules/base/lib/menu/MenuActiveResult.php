<?php

namespace base\menu;

class MenuActiveResult {
    
    protected $menu;
    protected $result;
    
    public function __construct($menu, $result) {
        
        $this->menu = $menu;
        $this->result = $result;
        
    }
    
    
    public function setMenu($m) { $this->menu = $m; }
    public function getMenu() { return $this->menu; }
    
    
    public function setResult($bln) { $this->result = $bln; }
    public function getResult() { return $this->result; }
    
    
}


