<?php

namespace core\forms;


class CodegenBaseForm extends BaseForm {
    
    protected $usedWidgetNames = array();
    
    
    public function __construct() {
        parent::__construct();
        
        
        
        $this->initCodegen();
    }
    
    public static function createForm($json) {
        $f = new CodegenBaseForm();
        $f->disableSubmit();
        
        if (is_array($json) == false)
            return $f;
        
        $f->addJsonItems( $json );
        
        return $f;
    }
    protected function addJsonItems($items, $parentWidget=null) {
        if ($parentWidget == null)
            $parentWidget = $this;
        
        for($x=0; $x < count($items); $x++) {
            $item = $items[$x];
            
            $classname = $item->data->class;
            
            // create object
            $rf = new \ReflectionClass($classname);
            $cm = $rf->getConstructor();
            
            if (isset($item->data->name) == false || $item->data->name == '' || in_array($item->data->name, $this->usedWidgetNames)) {
                if (isset($item->data->name) == false || $item->data->name == '') {
                    $name = 'default_name';
                } else {
                    $name = $item->data->name;
                }
                $no = 1;
                while (in_array($name.$no, $this->usedWidgetNames)) {
                    $no++;
                }
                $item->data->name = $name . $no;
            }
            
            $this->usedWidgetNames[] = $item->data->name;
            
            // build params-array for constructor parameters
            $params = array();
            foreach($cm->getParameters() as $func_param) {
                // optionItems? (SelectField etc)
                if ($func_param->name == 'optionItems') {
                    if (isset($item->data->optionItems))
                        $params[] = $this->optionsToArray( $item->data->optionItems );
                    else
                        $params[] = array();
                }
                // value found?
                else if (isset($item->data->{$func_param->name})) {
                    $params[] = $item->data->{$func_param->name};
                }
                // default parameter-value?
                else if ($func_param->isOptional()) {
                    $params[] = $func_param->getDefaultValue();
                }
                // default to null
                else {
                    $params[] = null;
                }
            }
            
            // initiate
            $obj = $rf->newInstanceArgs( $params );
            
            if (@$item->data->info_text) {
                $obj->setInfoText( $item->data->info_text );
            }
            
            // add
//             print "Widgetname: " . $obj->getName() . "\n";
            $parentWidget->addWidget($obj);
            
            
            if (isset($item->children) && count($item->children)) {
                $this->addJsonItems($item->children, $obj);
            }
        }
    }
    
    protected function optionsToArray($str) {
        $map = array();
        
        if (strpos($str, '<?') === 0) {
            try {
                if (strpos($str, '<?php') === 0)
                    $str = substr($str, 5);
                else
                    $str = substr($str, 2);
                
                $map = eval( $str );
            } catch(\Exception $ex) {
                $map[''] = 'Error: ' . $ex->getMessage();
            } catch (\Error $err) {
                $map[''] = 'Error: ' . $err->getMessage();
            }
        } else {
            $lines = explode("\n", $str);
            foreach($lines as $l) {
                $l = trim($l);
                if ($l == '') continue;
                
                if (strpos($l, ':') !== false) {
                    list($key, $val) = explode(':', $l, 2);
                } else {
                    $key = $val = $l;
                }
                $key = trim($key);
                $val = trim($val);
                $map[$key] = $val;
            }
        }
        
        return $map;
    }
    
    
    
    public function initCodegen() {
        
    }
    
    
}
