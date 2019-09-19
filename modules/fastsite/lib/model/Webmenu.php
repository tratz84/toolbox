<?php


namespace fastsite\model;


class Webmenu extends base\WebmenuBase {

    protected $children = array();
    
    
    public function getChildren() { return $this->children; }
    public function setChildren($c) { $this->children = $c; }
    public function addChild(Webmenu $m) { $this->children[] = $m; }
    public function clearChildren() { $this->children = array(); }
    
    
    
    public function getSummary() {
        $t = '';
        
        if ($this->getCode()) {
            $t = $this->getCode() . ' - ';
        }
        if ($this->getLabel()) {
            $t .= $this->getLabel();
        } else if ($this->getUrl()) {
            $t .= $this->getUrl();
        }
        
        if ($t == '') {
            $t = $this->getWebmenuId();
        }
        
        return $t;
    }

}

