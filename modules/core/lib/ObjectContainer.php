<?php

namespace core;

/**
 * ObjectContainer - all objects should be created by ObjectContainer for future purposes (proxying/filtering)
 * 
 */
use core\container\ObjectHookCall;
use core\container\ObjectHookProxy;
use core\container\ObjectHookable;
use core\db\DatabaseTransactionObject;
use core\event\EventBus;
use core\exception\DatabaseException;
use core\exception\InvalidStateException;

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
            $this->objects[$className] = $this->create( $className );
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
        
        // check hooks
        // TODO: something dynamic based on annotations?
        $isObjectHookable = is_subclass_of($className, ObjectHookable::class);
        $isDatabaseTransactionObject = is_subclass_of($className, DatabaseTransactionObject::class);
        
        // create class or proxy
        if (defined('ADMIN_CONTEXT') == false && $isObjectHookable) {
            $obj = ObjectHookProxy::createProxy($className, $params);
        }
        else {
            $obj = new $className( ... $params);
        }
        
        
        // filter Database transactions
        if (defined('ADMIN_CONTEXT') == false && $isDatabaseTransactionObject) {
            $obj->proxy_addFilter(function(ObjectHookCall $ohc) {
                $con = \core\db\DatabaseHandler::getInstance()->getConnection('default');
                
                // Form? => auto set lock for object
                $arguments = $ohc->getArguments();
                if ( count($arguments) && is_a($arguments[0], \core\db\LockableObject::class)
                    && $con->getTransactionCount() == 0 )
                {
                    $lockKey = $arguments[0]->getLockKey();
                    
                    if ($lockKey) {
                        if (!$con->getLock( $lockKey )) {
                            // failed to get lock..
                            throw new DatabaseException('Unable to get lock: ' . $lockKey);
                        }
                    }
                }
                
                // start transaction
                $con->beginTransaction();
                
                try {
                    $r = $ohc->next();
                    
                    $con->commitTransaction();
                    
                } catch (\Exception $ex) {
                    try {
                        $con->rollbackTransaction();
                    } catch (\Exception $ex2) { /* not caring about rollbackTransaction-exceptions at the moment */ }
                    
                    throw $ex;
                }
                
                // release locks, if set
                $con->releaseLocks();
                
                return $r;
            });
        }
        
        
        
        // filter pre-call- & post-call- hooks
        if (defined('ADMIN_CONTEXT') == false && $isObjectHookable) {
            $obj->proxy_addFilter(function(ObjectHookCall $ohc) {
                $eb = ObjectContainer::getInstance()->get(EventBus::class);
                
                $className = $ohc->getClassName();
                
                $eb->publishEvent($ohc, 'core', 'pre-call-'.$className.'::'.$ohc->getFunctionName());
                
                $r = $ohc->next();
                
                $eb->publishEvent($ohc, 'core', 'post-call-'.$className.'::'.$ohc->getFunctionName());
                
                return $r;
            });
                
                $this->objects[$className] = $obj;
        }
        
        // ->setObjectContainer( $this )
        if (method_exists($obj, 'setObjectContainer')) {
            $obj->setObjectContainer( $this );
        }
        
        
        // prevent recursive loop
        if ($obj instanceof \core\event\EventBus) {
            $eb = $obj;
        } else {
            $eb = ObjectContainer::getInstance()->get(EventBus::class);
        }
        
        // 
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

