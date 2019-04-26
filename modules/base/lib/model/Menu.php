<?php


namespace base\model;


use base\menu\MasterDataMenu;

class Menu extends base\MenuBase {

    protected $mapping = null;
    
    protected $weight = 10;
    
    public function __construct() {
        
    }
    
    public function setIconLabelUrl($icon, $label, $url, $weight=10) {
        $this->setField('icon',  $icon);
        $this->setField('label', $label);
        $this->setField('url',   $url);
        
        $this->weight = $weight;
    }
    
    
    public function getIcon() { return $this->getField('icon'); }
    
    public function setLabel($p) { $this->setField('label', $p); }
    public function getLabel() { return $this->getField('label'); }
    
    public function setUrl($p) { $this->setField('url', $p); }
    public function getUrl() { return $this->getField('url'); }
    
    public function getWeight() { return $this->weight; }
    public function setWeight($p) { $this->weight = $p; }
    
    
    public function isActive() {
        
        $url = '/' . substr($_SERVER['REQUEST_URI'], strlen(appUrl('/')));
        $controller = \core\Context::getInstance()->getController();
        
        if ($this->getUrl() == '/') {
            if ($controller == 'dashboard') {
                return true;
            }
        } else if (strpos($url, $this->getUrl()) === 0) {
            return true;
        }
        // hmz.. hacky, moet beter
        else if ($this->getUrl() == '/?m=rental&c=contract/view' && strpos($url, '/?m=rental&c=contract/wizard') === 0) {
            return true;
        } else if ($this->getUrl() == '/?m=base&c=masterdata/index') {
            $mdm = MasterDataMenu::generate();
            
            foreach($mdm->getMenu() as $section => $items) {
                foreach($items as $i) {
                    if (strpos($url, $i['url']) === 0) {
                        return true;
                    }
                }
            }
        }
        
        
        return false;
    }
    
}

