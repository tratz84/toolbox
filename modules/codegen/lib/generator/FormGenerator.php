<?php


namespace codegen\generator;


use core\exception\FileException;

class FormGenerator {
    
    protected $data;
    
    public function __construct() {
        
    }
    
    public function loadData($moduleName, $filename) {
        $fcheck = module_file($moduleName, '/config/codegen');
        if (!$fcheck)
            return false;
        
        $f = module_file($moduleName, '/config/codegen/'.$filename);
        if (strpos($f, $fcheck) !== 0)
            return false;
        
        $this->data = include $f;
    }
    
    public function setData() {
        
    }
    
    
    public function generate() {
        
        $this->generateFormFile();
        
        $this->insertCodegen();
    }
    
    public function getClassName() {
        $classname = slugify( $this->data['form_name'] );
        $classname = preg_replace_callback('/(-.)/', function($word) { return strtoupper(substr($word[0], 1)); }, $classname);
        $classname = ucfirst($classname);
        
        if (endsWith($classname, 'Form') == false) {
            $classname = $classname . 'Form';
        }
        
        return $classname;
    }
    
    public function generateFormFile() {
        $module = $this->data['module_name'];
        
        $path = module_file($module, '/lib/form/'.$classname.'.php');
        
        // file already exists?
        if ($path !== false) {
            return;
        }
        
        $classname = $this->getClassName();
        
        $vars = array();
        $vars['namespace'] = $module.'\\form';
        $vars['classname'] = $classname;
        $tpl = get_template(module_file('codegen', 'templates/_classes/codegenform-template.php'), $vars);
        
        $libdir = module_file($module, 'lib');
        if ($libdir == false) {
            throw new \core\exception\InvalidStateException('lib dir not found for module '.$module);
        }
        
        if (is_dir($libdir.'/form') == false) {
            if (mkdir($libdir.'/form', 0755, true) == false) {
                throw new FileException('Unable to create form dir, module, ' . $module);
            }
        }

        $formdir = module_file($module, 'lib/form');
        
        file_put_contents($formdir.'/'.$classname.'.php', $tpl);
    }
    
    
    
    public function insertCodegen() {
        
    }
    
    
    
}

