<?php


namespace core\forms;


use core\container\ObjectHookCall;
use core\db\DBObject;
use core\exception\InvalidStateException;

class WidgetContainer extends BaseWidget {
    
    protected $widgets = array();
    protected $widgetNames = array();
    
    protected $autoPrio = 10;
    
    protected $blnDoBinding = false;
    protected $bindHooks = array();
    
    public function __construct($name='widget-container') {
        $this->setName($name);
    }
    
    public function hookBind($callback) {
        $this->bindHooks[] = $callback;
    }
    
    public function addWidget($w) {
        $widgetName = $w->getName();
        
        // check duplicates
        if (isset($this->widgetNames[$widgetName])) {
            throw new InvalidStateException('Duplicate widget name: "'.$widgetName.'"');
        }
        
        $this->widgets[] = $w;
        $this->widgetNames[$widgetName] = true;
        
        if (!$w->getPrio()) {
            $w->setPrio( $this->autoPrio );
            $this->autoPrio += 10;
        }
    }
    public function addWidgets($widgets) {
        foreach($widgets as $w) {
            $this->addWidget($w);
        }
    }
    public function getWidgets() { return $this->widgets; }
    
    public function getWidgetsRecursive() {
        $allWidgets = array();
        
        $widgets = $this->getWidgets();
        foreach($widgets as $w) {
            if (is_a($w, WidgetContainer::class)) {
                $w = $w->getWidgetsRecursive();
            } else {
                $w = array($w);
            }
            foreach ($w as $w2) {
                $allWidgets[] = $w2;
            }
        }
        
        return $allWidgets;
    }
    
    public function getWidgetValue($name, $defaultVal=null) {
        $w = $this->getWidget($name);
        if ($w) {
            return $w->getValue();
        } else {
            return $defaultVal;
        }
    }
    
    public function getWidget($name) {
        foreach($this->widgets as $w) {
            if (is_a($w, WidgetContainer::class)) {
                $obj = $w->getWidget($name);
                if ($obj)
                    return $obj;
            }
            if ($w->getName() == $name) {
                return $w;
            }
        }
        
        return null;
    }
    
    public function removeWidget($name) {
        $widgets = array();
        
        foreach($this->widgets as $w) {
            if ($w->getName() == $name) {
                // found?
                unset( $this->widgetNames[$name] );
                continue;
            }
            
            $widgets[] = $w;
        }
        
        $this->widgets = $widgets;
    }
    
    
    
    /**
     * bind($obj) - binds $obj to form
     * @param unknown $obj
     * 
     * @return number of fields set
     */
    public function bind($obj) {
        $ohc = new ObjectHookCall($this, 'bind', array($obj));
        hook_eventbus_publish($ohc, 'core', 'pre-call-'.get_class($this).'::bind');
        
        
        $fieldCount = 0;
        
        if (is_admin_context() == false && is_a($this, BaseForm::class) && is_a($obj, DBObject::class)) {
            $this->setObjectLocked( dbobject_is_locked($obj) ? true : false );
        }
        
        // CheckboxField's aren't posted, set default to false
        if ($this->blnDoBinding == false) 
            foreach($this->widgets as $w) {
                if (is_a($w, CheckboxField::class))
                    $w->setValue(0);
            }
        
        foreach($this->widgets as $w) {
            $fieldCount += $w->bindObject($obj);
        }
        
        // prevent recursive loop self::bind(), hook might call bind() again..
        if ($this->blnDoBinding == false) {
            $this->blnDoBinding = true;
            
            foreach($this->bindHooks as $bh) {
                $r = $bh( $this );
                if ($r && is_numeric($r))
                    $fieldCount += $r;
            }
            $this->blnDoBinding = false;
        }
        
        $ohc->setReturnValue($fieldCount);
        hook_eventbus_publish($ohc, 'core', 'post-call-'.get_class($this).'::bind');
        
        return $fieldCount;
    }
    
    
    /**
     * fill($obj) - fills $obj with given fields
     * @param $obj
     */
    public function fill($obj, $fields=array()) {
        $ohc = new ObjectHookCall($this, 'bind', array($obj, $fields));
        hook_eventbus_publish($ohc, 'core', 'pre-call-'.get_class($this).'::fill');
        
        foreach($fields as $f) {
            $widget = $this->getWidget($f);
            
            if ($widget == null)
                continue;
            
            if (method_exists($widget, 'fill')) {
                $widget->fill( $obj );
            } else {
                $val = $widget->getValue();
                
                if (is_a($obj, DBObject::class)) {
                    
                    // fetch columnType for binding rules
                    $columnType = $obj->getColumnType( $f );
    
                    // set empty strings with type int to NULL
                    if (strpos($columnType, 'int') === 0 && $val === '') {
                        $val = null;
                    }
                    
                    
                    $setFunction = 'set'.dbCamelCase($f);
                    
                    if (method_exists($obj, $setFunction)) {
                        $obj->$setFunction( $val );
                    } else {
                        $obj->setField($f, $val);
                    }
                } if (is_array($obj)) {
                    $obj[$f] = $val;
                }
            }
        }
        
        hook_eventbus_publish($ohc, 'core', 'post-call-'.get_class($this).'::fill');
    }
    
    
    public function asArray($opts=array()) {
        $r = array();
        
        foreach($this->widgets as $w) {
            if (is_a($w, WidgetContainer::class)) {
                // TODO: remove 'flat' option & make it default?
                if (isset($opts['flat']) && $opts['flat']) {
                    $r = array_merge($r, $w->asArray());
                } else {
                    $r[$w->getName()] = $w->asArray();
                }
            } else {
                $r[$w->getName()] = $w->getValue();
            }
        }
        
        
        return $r;
    }
    
    
    public function changes(DBObject $obj) {
        $changes = array();
        foreach($this->widgets as $w) {
            if (is_a($w, HiddenField::class)) continue;
            
                
            
            if (is_a($w, ListFormWidget::class)) {
                $changes2 = $w->changes($obj);
                $changes = array_merge($changes, $changes2);
            }
            else if (is_a($w, ListEditWidget::class)) {
                $changes2 = $w->changes($obj);
                $changes = array_merge($changes, $changes2);
            }
            else if ($obj->hasDatabaseField($w->getName()) == false && $obj->hasField($w->getName()) == false) {
                // not a list & not a database-field? => skip
                continue;
            }
            
            $v1 = $obj->getField($w->getName());
            if ($v1 != $w->getValue()) {
                $changes[] = array(
                    'fieldname' => $w->getName(),
                    'old' => $v1,
                    'new' => $w->getValue()
                );
            }
        }
        
        return $changes;
    }
    
    protected function sortWidgets() {
        usort($this->widgets, function($w1, $w2) {
            return ($w1->getPrio() - $w2->getPrio())*100;
        });
    }
    
    
    public function render() {
        $this->sortWidgets();
        
        $html = '<div class="widget widget-container widget-container-'.slugify($this->getName()).'">';
        
        foreach($this->widgets as $w) {
            $html .= $w->render();
        }
        
        $html .= '</div>';
        
        return $html;
    }
    
    
    /**
     * setArrayPrefix() - sets prefix
     *
     * @param $prefix - string of function
     */
    public function setArrayPrefix( $prefix, $index=0 ) {
        $widgets = $this->getWidgetsRecursive();
        for($x=0; $x < count($widgets); $x++) {
            // skip empty named
            if ($widgets[$x]->getName() == '')
                continue;
            // save original name
            if (!$widgets[$x]->getField('originalWidgetName', false))
                $widgets[$x]->setField('originalWidgetName', $widgets[$x]->getName());
            
            
            // build name
            $n = $widgets[$x]->getField('originalWidgetName');
            if ($prefix)
                $n = $prefix . '[' . $index . ']'. '['.$n.']';
            
            $widgets[$x]->setName( $n );
        }
    }
                    
    public function setNamePrefix( $prefix ) {
        $widgets = $this->getWidgetsRecursive();
        for($x=0; $x < count($widgets); $x++) {
            // skip empty named
            if ($widgets[$x]->getName() == '')
                continue;
                
            // save original name
            if (!$widgets[$x]->getField('originalWidgetName', false))
                $widgets[$x]->setField('originalWidgetName', $widgets[$x]->getName());
                    
            // build name
            $n = $widgets[$x]->getField('originalWidgetName');
            
            if ($prefix)
                $n = $prefix . $n;
            
            $widgets[$x]->setName( $n );
        }
        
    }
    
    
}

