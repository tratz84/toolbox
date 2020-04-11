<?php


namespace core\controller;

use core\Context;
use core\event\EventBus;
use core\db\DatabaseHandler;



class BaseController {
    
    protected $templateFile = null;
    protected $decoratorFile = null;
    protected $showDecorator = false;
    
    protected $actionTemplate = null;
    
    protected $javascriptFiles = array();
    
    protected $moduleName = null;
    protected $controllerPath = null;
    
    protected $pageTitle = array();
    
    /**
     * @var \core\ObjectContainer
     */
    public $oc;
    
    
    public function __construct() {
        
    }
    
    public function addTitle($txt) { $this->pageTitle[] = $txt; }
    
    
    protected function getModuleName() {
        if ($this->moduleName == null) {
            $ref = new \ReflectionClass( get_class($this) );
            
            $filename_controller = $ref->getFileName();
            
            $dirs = \core\Context::getInstance()->getModuleDirs();
            $found = null;
            foreach($dirs as $d) {
                $d = realpath($d);
                if (strpos($filename_controller, $d) === 0) {
                    $found = $d;
                    break;
                }
            }
            
            // hmz
            if (!$found) {
                return null;
            }
            
            $f = substr($filename_controller, strlen($d)+1);
            $this->moduleName = substr($f, 0, strpos($f, DIRECTORY_SEPARATOR));
            $this->controllerPath = substr($f, strlen($this->moduleName.'/controller/'), '-4');
        }
        
        return $this->moduleName;
    }
    
    public function getControllerPath() {
        if ($this->controllerPath == null) {
            $this->getModuleName();
        }
        
        $p = $this->controllerPath;
        
        if (endsWith($p, 'Controller'))
            $p = substr($p, 0, strlen($p) - strlen('Controller'));
        
        return $p;
    }
    
    
    public function addJavascript($url) { $this->javascriptFiles[] = $url; }
    
    public function setTemplateFile($f) { $this->templateFile = $f; }
    public function setDecoratorFile($f) { $this->decoratorFile = $f; }
    public function setShowDecorator($bln) { $this->showDecorator = $bln; }
    
    public function setActionTemplate($p) { $this->actionTemplate = $p; }
    
    
    public function renderToString() {
        ob_start();
        
        $this->render();
        
        return ob_get_clean();
    }
    
    public function renderError($errorMessage) {
        DatabaseHandler::getInstance()->closeAll();
        
        $ctx = Context::getInstance();
        
        // derive current module from path
        $reflector = new \ReflectionObject($this);
        $filenameClass = $reflector->getFileName();
        
        $module = module_file2module($filenameClass);
        
        $templateFile = module_file('base', 'templates/decorator/handled_error.php');
        
        $vars = get_object_vars( $this );
        
        $tpl = new \core\template\DefaultTemplate($templateFile);
        foreach($vars as $key => $val) {
            $tpl->setVar($key, $val);
        }
        
        $tpl->setVar('errorMessage', $errorMessage);
        
        // parse outer template
        if ($this->decoratorFile == null) {
            $this->decoratorFile = module_file('base', 'templates/decorator/default.php');
        }
        
        $tplMaster = new \core\template\DefaultTemplate($this->decoratorFile);
        foreach($vars as $key => $val) {
            $tplMaster->setVar($key, $val);
        }
        $tplMaster->setVar( 'content', $tpl->getTemplate() );
        $tplMaster->setVar( 'context', $ctx );
        $tplMaster->setVar( 'body_class', 'module-'.slugify($module). ' ' . slugify($module) . '-' . slugify(get_class($this)) . ' action-'.slugify($this->actionTemplate) );
        
        $tplMaster->showTemplate();
        
    }
    
    public function render() {
        $ctx = Context::getInstance();
        
        // derive current module from path
        $reflector = new \ReflectionObject($this);
        $filenameClass = $reflector->getFileName();
        
        $module = module_file2module( $filenameClass );
        
        $modulePath = module_path( $module );
        $controllerDir = substr($reflector->getFileName(), strlen($modulePath . '/controller/'));
        $controllerDir = substr($controllerDir, 0, strpos($controllerDir, 'Controller.php'));
        
        if ($this->actionTemplate === null)
            $this->actionTemplate = $ctx->getAction();
        
        $vars = get_object_vars( $this );
        
        // parse (sub)template
        if ($this->templateFile == null) {
            $this->templateFile = module_file($module, 'templates/'.$controllerDir.'/'.$this->actionTemplate.'.php');
        }
        $tpl = new \core\template\DefaultTemplate($this->templateFile);
        $tpl->setVar('controller', $this);
        foreach($vars as $key => $val) {
            $tpl->setVar($key, $val);
        }
        
        
        // publish event
        $eb = $this->oc->get(EventBus::class);
        $eb->publishEvent($tpl, 'base', 'render-'.$module.'-'.get_class($this).'-'.$this->actionTemplate);
        
        
        if ($this->showDecorator == false) {
            $tpl->showTemplate();
            return;
        }
        
        // register javascript files
        if (count($this->javascriptFiles)) {
            $jsgroup = $module . '-' . get_class($this);
            hook_htmlscriptloader_enableGroup($jsgroup);
            
            foreach($this->javascriptFiles as $url) {
                hook_register_javascript($jsgroup, $url);
            }
        }
        
        // parse outer template
        if ($this->decoratorFile == null) {
            $this->decoratorFile = module_file('base', 'templates/decorator/default.php');
        }
        
        $tplMaster = new \core\template\DefaultTemplate($this->decoratorFile);
        foreach($vars as $key => $val) {
            $tplMaster->setVar($key, $val);
        }
        $tplMaster->setVar( 'content', $tpl->getTemplate() );
        $tplMaster->setVar( 'context', $ctx );
        $tplMaster->setVar( 'body_class', 'module-'.slugify($module). ' ' . slugify($module) . '-' . slugify(get_class($this)) . ' action-'.slugify($this->actionTemplate) );
        
        $tplMaster->showTemplate();
    }
    

    protected function objectsToArray($objs, $fields) {
        $list = array();
        
        foreach($objs as $obj) {
            $list[] = $this->objectToArray($obj, $fields);
        }
        
        return $list;
    }
    
    protected function objectToArray($obj, $fields) {
        $arr = array();
        
        foreach($fields as $f) {
            
            $func = 'get'.slugify($f);
            if (method_exists($obj, $func)) {
                $arr[$f] = $obj->$func();
            } else {
                $arr[$f] = $obj->getField($f);
            }
        }
        
        return $arr;
    }
    
    
    public function json($arr) {
        
        header('Content-type: application/json; charset=utf-8');
        
        print json_encode( $arr );
        
    }
    
}

