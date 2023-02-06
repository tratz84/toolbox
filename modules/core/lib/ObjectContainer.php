<?php

namespace core;

/**
 * ObjectContainer - all objects should be created by ObjectContainer for future purposes (proxying/filtering)
 * 
 */
use core\db\DatabaseTransactionObject;
use core\db\DatabaseTransactionProxy;
use core\event\EventBus;
use core\exception\InvalidStateException;
use core\service\ServiceBase;
use core\container\ObjectHookable;
use core\container\ObjectHookProxy;
use core\container\ObjectHookProxyExtender;

class ObjectContainer {
    
    protected $objects = array();
    
    protected static $instance = null;
    
    protected $classNameRewrite = array();
    
    
    public function get($className) {
        
        // classname rewriter, helps 'override' existings classes
        if (isset($this->classNameRewrite[$className])) {
            $className = $this->classNameRewrite[$className];
        }
        
        
        if (isset($this->objects[$className]) == false) {
            
            $isObjectHookable = is_subclass_of($className, ObjectHookable::class);
            $isDatabaseTransctionObject = is_a($className, DatabaseTransactionObject::class);
            
            if (defined('ADMIN_CONTEXT') == false && $isObjectHookable) {
                $obj = ObjectHookProxyExtender::createProxy($className);
                
                $this->objects[$className] = $obj;
                
            }
            else {
                $this->objects[$className] = new $className();
            }
            
            if (method_exists($this->objects[$className], 'setObjectContainer')) {
                $this->objects[$className]->setObjectContainer( $this );
            }
        }
        
        return $this->objects[$className];
    }
    
    public function create($className) {
        $params = array();
        
        if (func_num_args() > 1) {
            $params = func_get_args();
            $params = array_splice($params, 1);
        }
        
        
        // classname rewriter, helps 'override' existings classes
        if (isset($this->classNameRewrite[$className])) {
            $className = $this->classNameRewrite[$className];
        }
        
        $obj = new $className(...$params);
        
        $isObjectHookable = is_a($obj, ObjectHookable::class);
        
        if (defined('ADMIN_CONTEXT') == false && $isObjectHookable) {
            $obj = new ObjectHookProxy( $obj );
        }
        
        $eb = ObjectContainer::getInstance()->get(EventBus::class);
        $eb->publishEvent($obj, 'core', 'create-'.$className);
        
        return $obj;
    }
    
    public function rewriteClassName($className, $newClassName) {
        $this->classNameRewrite[$className] = $newClassName;
    }
    
    
    public function setObject($className, $object) {
        $this->objects[$className] = $object;
    }
    
    
    public function getController($module, $controller) {
        $ctx = Context::getInstance();
        
        if ( $ctx->isModuleEnabled($module) == false ) {
            throw new InvalidStateException('Requested module not enabled ('.$module.')');
        }
        
        if (endsWith($controller, 'Controller')) {
            $controller = substr($controller, 0, strpos($controller, 'Controller'));
        }
        
        
        // include controller class
        $p = module_file($module, 'controller/'.$controller.'Controller.php');
        
        if ($p == false)
            throw new \Exception('Controller '.$controller.' not found');
        
        require_once $p;
        
        // instantiate
        $controllerClassname = substr($controller, strrpos($controller, '/')) . 'Controller';
        $controllerClassname = trim($controllerClassname, '/');
        $controllerInstance = new $controllerClassname();
        
        $controllerInstance->oc = $this;
        $controllerInstance->ctx = $ctx;
        $controllerInstance->user = $ctx->getUser();
        
        // call init if it exists
        if (method_exists($controllerInstance, 'init'))
            $controllerInstance->init();
        
        return $controllerInstance;
    }
    
    
    public static function getInstance() {
        if (self::$instance == null) {
            self::$instance = new ObjectContainer();
        }
        
        return self::$instance;
    }
    
    
}

