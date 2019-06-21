<?php


namespace core\forms;



use core\db\DBObject;

class BaseForm extends WidgetContainer {
    
    protected $htmlAttributes = array();
    
    protected $validators = array();
    
    protected $objectLocked = false;
    
    protected $errors = array();
    
    protected $enctype = "application/x-www-form-urlencoded";
    
    protected $keyFields = array();
    
    protected $javascript = array();
    
    
    public function __construct() {
        
        $this->setHtmlAttribute('data-form-class', get_class($this));
        
    }
    
    public static function createAndBind(DBObject $obj) {
        $clazzName = static::class;
        
        $frm = object_container_create( $clazzName );
        $frm->bind( $obj );
        
        return $frm;
    }
    
    
    public function setObjectLocked($bln) { $this->objectLocked = $bln; }
    public function isObjectLocked() { return $this->objectLocked ? true : false; }
    
    
    public function addJavascript($name, $script) {
        $this->javascript[$name] = $script;
    }
    public function removeJavascript($name) {
        if (isset($this->javascript[$name])) {
            unset($this->javascript[$name]);
        }
    }
    
    /**
     * 
     */
    public function addKeyField($fieldName) {
        $this->keyFields[] = $fieldName;
    }
    
    public function renderKeyFields() {
        $html = '';
        
        foreach($this->keyFields as $f) {
            $html .= '<input type="hidden" class="key-field" value="'.esc_attr($f).'" />' . "\n";
        }
        
        return $html;
    }
    
    protected function enctypeToMultipartFormdata() {
        $this->enctype = 'multipart/form-data';
    }
    
    public function disableSubmit() {
        $this->setHtmlAttribute('onsubmit', 'return false;');
    }
    
    public function setHtmlAttribute($name, $val) {
        $this->htmlAttributes[$name] = $val;
    }
    public function getHtmlAttribute($name) {
        if (isset($this->htmlAttributes[$name]))
            return $this->htmlAttributes[$name];
        else
            return null;
    }
    
    
    public function removeWidget($name) {
        parent::removeWidget($name);
        
        $this->removeValidator($name);
    }
    
    
    /**
     * 
     * @param string $name
     * @param \core\forms\BaseValidator|function $val
     *         - function must return error message OR null on success
     */
    public function addValidator($name, $val) {
        if (isset($this->validators[$name]) == false)
            $this->validators[$name] = array();
        
        $this->validators[$name][] = $val;
    }
    
    public function removeValidator($name, $class=null) {
        if (isset($this->validators[$name]) == false)
            return;
        
        if ($class === null) {
            unset($this->validators[$name]);
        } else {
            $vals = array();
            foreach($this->validators[$name] as $v) {
                if (is_a($v, $class) == false) {
                    $vals[] = $v;
                }
            }
            
            $this->validators[$name] = $vals;
        }
    }
    
    
    public function addError($field, $message) {
        if (isset($this->errors[$field]) == false)
            $this->errors[$field] = array();
        
        $this->errors[$field][] = $message;
    }
    
    public function getLabelByFieldname($fieldName) {
        $w = $this->getWidget($fieldName);
        
        if ($w) {
            return $w->getLabel();
        } else {
            return '';
        }
    }
    
    public function getErrors() { return $this->errors; }
    
    public function getErrorList() {
        $l = array();
        
        foreach($this->errors as $field => $val) {
            foreach($val as $msg) {
                $l[] = $this->getLabelByFieldname($field) . ' - ' . $msg;
            }
        }
        
        return $l;
    }
    
    public function getErrorsForJson() {
        $r = array();
        
        foreach($this->errors as $field => $val) {
            foreach($val as $msg) {
                $r[] = array(
                    'field' => $field,
                    'label' => $this->getLabelByFieldname($field),
                    'message' => $msg
                );
            }
        }
        
        return $r;
    }
    
    
    public function validate() {
        
        foreach($this->validators as $fieldName => $validators) {
            
            $this->validateWidget($fieldName);
            
        }
        
        
        return count($this->errors) == 0 ? true : false;
    }
    
    public function validateWidget($fieldName) {
        $validators = $this->validators[$fieldName];
        $widget = $this->getWidget($fieldName);
        
        foreach($validators as $v) {
            
            if (is_callable($v)) {
                $msg = $v($this);
                
                if ($msg !== null) {
                    $this->addError($fieldName, $msg);
                    
                    $widget->hasError(true);
                }
                
            } else if ($v->validate($widget) == false) {
                $this->addError($fieldName, $v->getMessage());
                
                $widget->hasError(true);
            }
        }
    }
    
    
    
    protected function sortWidgets() {
        usort($this->widgets, function($w1, $w2) {
            return $w1->getPrio() > $w2->getPrio();
        });
    }
    
    public function renderReadonly() {
        $this->sortWidgets();
        
        $html = '<div class="form-generator form-readonly">';
        
        $className = get_class($this);
        if (strrpos($className, '\\') !== false)
            $className = substr($className, strrpos($className, '\\')+1);
            
        foreach($this->widgets as $w) {
            $html .= $w->renderAsText();
        }
        
        $html .= '</div>';
        
        return $html;
    }
    
    public function render() {
        $this->sortWidgets();
        
        $className = get_class($this);
        if (strrpos($className, '\\') !== false)
            $className = substr($className, strrpos($className, '\\')+1);
        
        $html = '';
        
        if (count($this->errors)) {
            $html .= '<div class="errors error-list">';
            $html .= '<ul>';
            foreach($this->errors as $fieldName => $errorList) {
                foreach($errorList as $e) {
                    $html .= '<li>'.$this->getLabelByFieldname($fieldName) . ' - ' . esc_html($e).'</li>';
                }
            }
            $html .= '</ul>';
            $html .= '</div>';
        }
        
        
        $html .= '<form method="POST" enctype="'.$this->enctype.'" class="form-generator form-'.slugify($className).'" ';
        
        foreach($this->htmlAttributes as $key => $val) {
            $html .= ' ' . esc_attr($key) . '="' . esc_attr($val) . '"';
        }
        $html .= '>';
        $html .= '<input type="hidden" class="object-locked" name="object-locked" value="'.($this->isObjectLocked()?'1':'0').'" />';
        $html .= '<input type="hidden" name="form-name" value="'.slugify($className).'" />' . "\n";
        $html .= $this->renderKeyFields();
        
        
        foreach($this->widgets as $w) {
            $html .= $w->render() . "\n";
        }
        
        $html .= '<div class="submit-container"><input type="submit" value="Opslaan" /></div>' . "\n";
        
        $html .= '</form>' . "\n\n";
        
        foreach($this->javascript as $key => $url) {
            if (strpos($url, '/') === 0) {
                $url = substr($url, 1);
            }
            
            $jsPath = null;
            // append filetime
            if (strpos($url, 'module/') === 0) {
                $jsPath = public_module_file_by_url($url);
            } else {
                $jsPath = realpath( WWW_ROOT . '/' . $url );
            }
            if ($jsPath && file_exists($jsPath))
                $url = $url . '?v='.filemtime($jsPath);
            
            $html .= '<script src="'.BASE_HREF.$url.'"></script>'."\n";
        }
        
        return $html;
    }
    
}

