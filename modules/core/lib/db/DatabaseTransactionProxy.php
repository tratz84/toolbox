<?php


namespace core\db;


use core\exception\MethodNotFoundException;
use core\container\ObjectHookProxy;
use core\forms\BaseForm;

class DatabaseTransactionProxy {
    
    protected $obj;
    
    public function __construct($obj) {
        $this->obj = $obj;
    }
    
    
    public function __call($name, $arguments) {
        if (is_a($this->obj, ObjectHookProxy::class) == false && method_exists($this->obj, $name) == false) {
            throw new MethodNotFoundException('method not found: ' . get_class($this->obj) . '::' . $name);
        }
        
        $con = \core\db\DatabaseHandler::getInstance()->getConnection('default');
        
        // Form? => auto set lock for object
        if (count($arguments) && is_a($arguments[0], \core\db\LockableObject::class)) {
            $lockKey = $arguments[0]->getLockKey();
            
            if ($lockKey) {
                $con->getLock( $lockKey );
            }
        }
        
        // start transaction
        $con->beginTransaction();
        
        try {
            $r = call_user_func_array(array($this->obj, $name), $arguments);
            
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
    }
    
}

