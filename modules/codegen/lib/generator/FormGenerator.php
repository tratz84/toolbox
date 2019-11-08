<?php


namespace codegen\generator;


use core\exception\FileException;
use codegen\parser\PhpCodeParser;

class FormGenerator {
    
    protected $data;
    
    protected $usedVarNames = array();
    protected $widgetNo = 1;
    
    protected $anonymousFunctions = array();
    
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
        
        return true;
    }
    
    public function delete($moduleName, $filename) {
        
        $this->loadData($moduleName, $filename);
        
        $classname = $this->getClassName();
        
        $path = module_file($moduleName, '/lib/form/'.$classname.'.php');
        
        if ($path) {
            unlink($path);
        }

        $f = module_file($moduleName, '/config/codegen/'.$filename);
        if ($f) {
            unlink($f);
        }
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
        
        $classname = $this->getClassName();
        
        $path = module_file($module, '/lib/form/'.$classname.'.php');
        
        // file already exists?
        if ($path !== false) {
            return true;
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
        $module = $this->data['module_name'];
        $classname = $this->getClassName();
        
        $path = module_file($module, '/lib/form/'.$classname.'.php');
        $json = json_decode( $this->data['treedata'] );
        
        $code = $this->addJsonItems( $json );
        
        
        $pcp = new PhpCodeParser();
        $pcp->parse( $path );
        $pcp->setFunction($classname.'::codegen', null, $code);
        
        $phpcode = $pcp->toString();
        
//         print $phpcode;
        file_put_contents($path, $phpcode);
    }
    
    protected function addJsonItems($items, $parentVariable=null) {
        $html = '';
        
        for($x=0; $x < count($items); $x++) {
            $item = $items[$x];
            
            $classname = $item->data->class;
            
            // create object
            $rf = new \ReflectionClass($classname);
            $cm = $rf->getConstructor();
            
            // build params-array for constructor parameters
            $params = array();
            $lastNonDefaultParam = 0;
            $cnt=0;
            foreach($cm->getParameters() as $func_param) {
                // optionItems? (SelectField etc)
                if ($func_param->name == 'optionItems') {
                    $params[] = $this->optionsToArray( $item->data->optionItems);
                    $lastNonDefaultParam = $cnt;
                }
                // value found?
                else if (isset($item->data->{$func_param->name})) {
                    $params[] = var_export($item->data->{$func_param->name}, true);
                    $lastNonDefaultParam = $cnt;
                }
                // default parameter-value?
                else if ($func_param->isOptional()) {
                    $params[] = var_export($func_param->getDefaultValue(), true);
                }
                // default to null
                else {
                    $params[] = var_export(null, true);
                    $lastNonDefaultParam = $cnt;
                }
                
                $cnt++;
            }
            
            
            
            // initiate
            $varname = '$w'.$this->widgetNo;
            $this->widgetNo++;
            
            $html .= $varname.' = new \\' . $classname . '(';
            for($z=0; $z < count($params) && $z <= $lastNonDefaultParam; $z++) {
                if ($z > 0) $html .= ', ';
                $html .= $params[$z];
            }
            $html .= ');' . PHP_EOL;
            
            $html .= ($parentVariable?$parentVariable:'$this').'->addWidget( '.$varname.' );' . PHP_EOL;
            
            if (@$item->data->info_text) {
                $html .= $varname.'->setInfoText( '.var_export($item->data->info_text, true).' );' . PHP_EOL;
            }
            
            if (isset($item->children) && count($item->children)) {
                $html .= "\n";
                $html .= $this->addJsonItems($item->children, $varname);
            }
        }
        
        if ($parentVariable == null) {
            $html = implode("\n", $this->anonymousFunctions) . "\n\n" . $html;
        }
        
        return $html;
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
            
            $funcname = '$func'.(count($this->anonymousFunctions)+1);
            
            $this->anonymousFunctions[] = $funcname.' = function() { ' . $str . ' }; ';
            
            return $funcname.'()';
        } else {
            $lines = explode("\n", $str);
            foreach($lines as $l) {
                $l = trim($l);
                if ($l == '') continue;
                
                list($key, $val) = explode(':', $l, 2);
                $key = trim($key);
                $val = trim($val);
                $map[$key] = $val;
            }
            
            return var_export($map, true);
        }
        
    }
    
    
    
    
}

