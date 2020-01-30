<?php

namespace codegen;

class CodegenModuleSettings {
    
    protected $module;
    protected $data;
    
    public function __construct($module) {
        $this->module = $module;
    }
    
    public function setVar($name, $val) {
        $this->data[$name] = $val;
    }
    public function getVar($name, $defaultValue=null) {
        if (isset($this->data[$name])) {
            return $this->data[$name];
        } else {
            return $defaultValue;
        }
    }
    
    public function save() {
        $f = module_file($this->module, '/');
        if (file_exists($f.'/config') == false) {
            mkdir($f . '/config', 0755);
        }
        
        $r = file_put_contents($f . '/config/codegen.php', "<?php\n\nreturn ".var_export($this->data, true).";\n\n");
        
        return $r !== false;
    }
    
    public function load() {
        $this->data = array();
        
        $f = module_file($this->module, '/config/codegen.php');
        if ($f) {
            $this->data = include $f;
        }
    }
    
}
