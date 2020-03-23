<?php


namespace core\forms;

use core\db\DBObject;
use core\exception\InvalidStateException;

class BaseWidget {
    
    protected $tagName = 'input';
    protected $attributes = array();
    protected $containerClasses = array();
    
    protected $name;
    protected $label;
    protected $value;
    protected $blnError;
    protected $arrRenderFunctions = array();
    
    protected $infoText = null;
    
    protected $fields = array();
    
    protected $prio = 0;
    
    public function getName() { return $this->name; }
    public function setName($n) { $this->name = $n; }
    
    public function getLabel() { return $this->label; }
    public function setLabel($n) { $this->label = $n; }
    
    public function getValue() { return $this->value; }
    public function setValue($n) { $this->value = $n; }
    
    public function setInfoText($t) { $this->infoText = $t; }
    public function getInfoText() { return $this->infoText; }
    
    public function setPrio($p) { $this->prio = $p; }
    public function getPrio() { return $this->prio; }
    
    public function addContainerClass($className) {
        if (in_array($className, $this->containerClasses) == false) {
            $this->containerClasses[] = $className;
        }
    }
    
    public function setTagName($p) { $this->tagName = $p; }
    public function getTagName() { return $this->tagName; }
    
    public function setAttribute($name, $val) { $this->attributes[$name] = $val; }
    public function getAttribute($name, $defaultVal=false) { return isset($this->attributes[$name]) ? $this->attributes[$name] : $defaultVal; }
    public function unsetAttribute($name) { if (isset($this->attributes[$name])) unset($this->attributes[$name]); }
    
    public function setField($field, $val) { $this->fields[$field] = $val; }
    public function getField($field, $defaultVal=null) {
        if (isset($this->fields[$field]))
            return $this->fields[$field];
        else
            return $defaultVal;
    }
    
    public function hasError($bln=null) {
        if ($bln !== null)
            $this->blnError = $bln;
        
        return $this->blnError;
    }
    
    public function bindObject($obj) {
        $arr = array();
        
        if (is_a($obj, DBObject::class)) {
            $arr = $obj->getFields();
        } else if (is_array($obj)) {
            $arr = $obj;
        } else {
            throw new InvalidStateException('Invalid object given');
        }
        
        $fieldCount=0;
        
        if (is_a($this, WidgetContainer::class)) {
            $fieldCount += $this->bind( $obj );
        } else {
            // first try getter
            $func = 'get'.dbCamelCase($this->getName());
            if (is_a($obj, DBObject::class) && method_exists($obj, $func)) {
                $this->setValue($obj->$func());
                $fieldCount++;
            }
            // field set?
            else if (isset($arr[$this->getName()])) {
                $this->setValue( $arr[$this->getName()] );
                
                $fieldCount++;
            }
        }
        
        return $fieldCount;
    }
    
    
    public function setRenderFunction($fieldName, $callback) {
        $this->arrRenderFunctions[$fieldName] = $callback;
    }
    
    public function render() {
        $this->addContainerClass('widget');
        $this->addContainerClass( slugify(get_class($this)) );
        
        // remove var/index
        $className = $this->getName();
        $posRightBracket = strrpos($className, ']');
        if ($posRightBracket !== false)
            $className = substr($className, $posRightBracket);
        
        $this->addContainerClass( slugify($className) . '-widget' );
        
        if ($this->getName()) {
            $this->setAttribute('name', $this->getName());
        }
        
        $html = '';
        $html .= '<div class="'.implode(' ', $this->containerClasses).'">';
        $html .= '<label>'.esc_html($this->getLabel()).infopopup($this->getInfoText()).'</label>';
        $html .= $this->renderTag();
        $html .= '</div>';
        
        return $html;
    }
    
    public function renderTag() {
        $tag = '<'.$this->tagName . ' ';
        foreach($this->attributes as $name => $val) {
            $tag .= esc_attr($name).'="'.esc_attr($val).'" ';
        }
        $tag .= ' />';
        
        return $tag;
    }
    
    public function renderAsText() {
        $html = '';
        
        $html .= '<div class="widget html-field-widget widget-'.slugify($this->getName()).'">';
        $html .= '<label>'.esc_html($this->getLabel()) . infopopup($this->getInfoText()) . '</label>';
        $html .= '<span class="widget-value">'.esc_html($this->getValue()).'</span>';
        $html .= '</div>';
        
        return $html;
        
    }
    
}

