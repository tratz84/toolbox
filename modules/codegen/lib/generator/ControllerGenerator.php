<?php

namespace codegen\generator;

use core\exception\FileException;
use core\exception\InvalidStateException;

class ControllerGenerator {
    
    protected $moduleName;
    protected $controllerName;
    protected $defaultActions=array();
    
    public function __construct($moduleName=null, $controllerName=null) {
        $this->setModuleName( $moduleName );
        $this->setControllerName( $controllerName );
    }
    
    public function getModuleName() { return $this->moduleName; }
    public function setModuleName($n) {
        if ($n != null && preg_match('/^[a-zA-Z0-9_]+$/', $n) == false) throw new InvalidStateException('Invalid module name');
        $this->moduleName = $n;
    }
    
    public function getControllerName() { return $this->controllerName; }
    public function setControllerName($n) {
        if ($n != null && preg_match('/^[a-zA-Z0-9_\\/]+$/', $n) == false) throw new InvalidStateException('Invalid module name');
        $this->controllerName = $n;
    }
    
    
    
    public function addAction($a) {
        $a = trim($a);
        
        if (preg_match('/^[a-z_]+$/', $a)) {
            $this->defaultActions[] = $a;
            return true;
        } else {
            return false;
        }
    }
    
    
    
    public function generate() {
        
        $controllerPath = module_file($this->moduleName, '/controller');
        
        $c_path = '/';
        $c_name = $this->controllerName;
        $c_name = substr($c_name, 0, strrpos($c_name, 'Controller'));
        
        if (strpos($this->controllerName, '/') !== false) {
            $c_name = substr($this->controllerName, strrpos($this->controllerName, '/')+1);
            $c_name = substr($c_name, 0, strrpos($c_name, 'Controller'));
            
            $c_path = '/'.substr($this->controllerName, 0, strrpos($this->controllerName, '/')+1);
        }
        

        // create controller-dir
        $fullControllerPath = $controllerPath . $c_path;
        if (file_exists($fullControllerPath) == false) {
            if (mkdir($fullControllerPath, 0755, true) == false) {
                throw new FileException('Unable to create controller-dir');
            }
        }
        
        // create controller-file
        $phpcode_controller = get_template(module_file('codegen', '/templates/_classes/codegen-controller.php'), [
            'controllerName' => $c_name,
            'actions' => $this->defaultActions
        ]);
        file_put_contents($fullControllerPath.$c_name.'Controller.php', $phpcode_controller);
        
        
        // create template-dir
        $template_dir = module_file($this->moduleName, '/templates');
        $template_dir = $template_dir . $c_path . '/' . $c_name;
        if (file_exists($template_dir) == false) {
            if (mkdir($template_dir, 0755, true) == false) {
                throw new FileException('Unable to create template-dir');
            }
        }
        
        // create template files
        foreach($this->defaultActions as $a) {
            // search is mostly a json-api
            if (in_array($a, ['search', 'delete'])) continue;
            
            
            $tplfile = module_file('codegen', '/templates/_classes/codegen-controller-'.$a.'.php');
            $phpcode_action = '';
            if ($tplfile) {
                $phpcode_action = get_template($tplfile, [
                    'controllerName' => $c_name,
                    'actions' => $this->defaultActions
                ]);
            }
            
            file_put_contents($template_dir . '/' . $a . '.php', $phpcode_action);
        }
        
    }
    
}

