<?php


namespace codegen\generator;


use core\exception\FileException;
use codegen\parser\PhpCodeParser;

class ListFormGenerator {
    
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
        
        $this->generateEditorFile();
        
        $this->insertCodegen();
    }
    
    public function getClassName() {
        $classname = slugify( $this->data['name'] );
        $classname = preg_replace_callback('/(-.)/', function($word) { return strtoupper(substr($word[0], 1)); }, $classname);
        $classname = ucfirst($classname);
        
//         if (endsWith($classname, 'List') == false) {
//             $classname = $classname . 'List';
//         }
        
        return $classname;
    }
    
    public function generateEditorFile() {
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
        $vars['formClass'] = $this->data['form_class'];
        $tpl = get_template(module_file('codegen', 'templates/_classes/codegen-listformwidget-template.php'), $vars);
        
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
        
        $code = '';
        
        // sortable?
        if (@$this->data['sortable']) {
            $code .= '$this->setSortable( true );' . PHP_EOL;
        }
        
        
        // set fields
        $fields = array();
        $fieldLabels = array();
        foreach($this->data['fields'] as $arr) {
            $fields[] = $arr['fieldname'];
            $fieldLabels[] = $arr['label'];
        }
        
        $code .= '$this->setFields(' . var_export( $fields, true ) . ');' . PHP_EOL;
        $code .= '$this->setFieldLabels(' . var_export( $fieldLabels, true ) . ');' . PHP_EOL;
        
        
        // set public-fields
        $publicFields = array();
        foreach($this->data['publicfields'] as $pf) {
            $publicFields[] = $pf['publicfieldname'];
        }
        $code .= '$this->setPublicFields(' . var_export( $publicFields, true ) . ');' . PHP_EOL;
        
        
        
        $path = module_file($module, '/lib/form/'.$classname.'.php');
        
        
        $pcp = new PhpCodeParser();
        $pcp->parse( $path );
        $pcp->setFunction($classname.'::codegen', null, $code);
        
        // class::codegenDbMapper-function
        if ($this->data['daoObject']) {
            $mapping_code = '$fdm = new \\core\\service\\FormDbMapper( self::class, \\'.$this->data['daoObject'].'::class );' . PHP_EOL;
            $mapping_code .= 'return $fdm;';
            $pcp->setFunction($classname.'::codegenDbMapper', null, $mapping_code, ['static' => true]);
            
            // generate ::getDbMapper()-function for programmer to adjust
            if ($pcp->getFunctionCode($classname.'::getDbMapper') === null) {
                $pcp->setFunction($classname.'::getDbMapper', null, '$m = self::codegenDbMapper();'.PHP_EOL.'return $m;', ['static' => true]);
            }
        }
        
        $phpcode = $pcp->toString();
        
        //         print $phpcode;
        file_put_contents($path, $phpcode);
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

