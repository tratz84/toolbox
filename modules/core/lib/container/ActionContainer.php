<?php

namespace core\container;

class ActionContainer {
    
    protected $objectType;
    protected $objectId;
    
    protected $items = array();
    
    public function __construct($objectType, $objectId) {
        $this->objectType = $objectType;
        $this->objectId = $objectId;
    }
    
    
    public function getObjectType() { return $this->objectType; }
    public function getObjectId() { return $this->objectId; }
    
    
    public function hasItems() {
        return count($this->items) > 0 ? true : false;
    }
    
    public function removeByName($name) {
        $newItems = array();
        
        foreach($this->items as $i) {
            if (isset($i['name']) && $i['name'] == $name)
                continue;
            
            $newItems[] = $i;
        }
        
        $this->items = $newItems;
    }
    
    public function addItem($name, $html, $prio=10) {
        $this->items[] = array(
            'name' => $name,
            'html' => $html
        );
    }
    
    public function getItems() {
        return $this->items;
    }
    
    
    public function render() {
        if (!$this->hasItems()) {
            return '';
        }
        
        $items = $this->getItems();
        
        $html = '<div class="action-box '.slugify($this->objectType).'">';
        for($x=0; $x < count($items); $x++) {
            $html .= '<span class="'.slugify($items[$x]['name']).'">' . $items[$x]['html'] . '</span> ';
        }
        $html .= '</div>';
        $html .= '<hr/>';
        
        return $html;
    }
    
    
}

