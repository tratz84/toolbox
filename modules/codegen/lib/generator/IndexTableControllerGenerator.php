<?php

namespace codegen\generator;

use codegen\parser\PhpCodeParser;


class IndexTableControllerGenerator {
    
    protected $data = array();
    
    public function loadData($moduleName, $filename) {
        $f = module_file_safe($moduleName, '/config/codegen', $filename);
        
        if (!$f) {
            return false;
        }
        
        $this->data = include $f;
        
        return true;
    }
    
    public function generateControllerFile() {
        $module_name = $this->data['module_name'];
        $controller_path = '/';
        $controller_name = $this->data['controller_name'];
        if (strpos($controller_name, '/') !== false) {
            $controller_path = '/'.substr($controller_name, 0, strrpos($controller_name, '/')) . '/';
            $controller_name = substr($controller_name, strrpos($controller_name, '/')+1);
        }
        
        $path = module_file($module_name, '/controller'.$controller_path.$controller_name.'.php');
        if ($path != false) {
            return true;
        }
        
        $vars = array();
        $vars['controller_name'] = $controller_name;
        $tpl = get_template(module_file('codegen', 'templates/_classes/codegen-indextable-controller.php'), $vars);
        
        $cdir = module_file($module_name, 'controller');
        if ($cdir == false) {
            throw new \core\exception\InvalidStateException('controller dir not found for module '.$module_name);
        }
        
        if (is_dir($cdir.$controller_path) == false) {
            if (mkdir($cdir.$controller_path, 0755, true) == false) {
                throw new FileException('Unable to create controller dir, module, ' . $module);
            }
        }
        
        $dir = $cdir . $controller_path;
        
        file_put_contents($dir.'/'.$controller_name.'.php', $tpl);
    }
    
    public function generateTemplateFile() {
        $module_name = $this->data['module_name'];
        
        $template_path = $this->data['controller_name'];
        
        $path = module_file_safe($module_name, '/templates/', $template_path);
        if ($path != false) {
            return true;
        }
        
        $path = '/'.substr($template_path, 0, strlen($template_path)-strlen('Controller'));
        
        $vars = array();
        $tpl = get_template(module_file('codegen', 'templates/_classes/codegen-indextable-index.php'), $vars);
        
        $tdir = module_file($module_name, 'templates');
        if ($tdir == false) {
            throw new \core\exception\InvalidStateException('template dir not found for module '.$module_name);
        }
        
        if (is_dir($tdir.$path) == false) {
            if (mkdir($tdir.$path, 0755, true) == false) {
                throw new FileException('Unable to create controller dir, module, ' . $module);
            }
        }
        
        $dir = $tdir.$path;
        
        file_put_contents($dir.'/index.php', $tpl);
    }
    
    public function generate() {
        // generate controller-file
         $this->generateControllerFile();
         
         // generate index template-fiel
         $this->generateTemplateFile();
        
        $path = module_file_safe($this->data['module_name'], 'controller', $this->data['controller_name'].'.php');
        if (!$path) {
            throw new InvalidStateException('Controller file not found');
        }
        
         $pcp = new PhpCodeParser();
         $pcp->parse( $path );
         
         $controller_name = $this->data['controller_name'];
         if (strpos($controller_name, '/') !== false) {
             $controller_name = substr($controller_name, strrpos($controller_name, '/')+1);
         }
         
         
        // set DAO class
        $pcp->setClassVar($controller_name.'::$daoClass', $this->data['dao_class'].'::class');
        
        // set query
        $pcp->setClassVar($controller_name.'::$query', var_export($this->data['query'], true));
        
        // set Php-code
        $pcp->setFunction($controller_name.'::renderRow', null, $this->data['phpcode']."\nreturn parent::renderRow(\$row);");
        
        // html-column-stuff
        $pcp->setClassVar($controller_name.'::$htmlColumns', var_export($this->data['htmlColumns'], true));
        
        // IndexTable columns
        $pcp->setClassVar($controller_name.'::$indexTableColumns', var_export($this->data['webColumns'], true));
        
        // export columns
        $pcp->setClassVar($controller_name.'::$exportColumns', var_export($this->data['exportColumns'], true));
        
        $phpcode = $pcp->toString();
        
        if (file_put_contents($path, $phpcode)) {
            return true;
        } else {
            return false;
        }
    }
    
}
