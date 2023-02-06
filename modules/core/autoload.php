<?php


use core\container\ObjectHookCall;
use core\exception\DatabaseException;
use core\service\ServiceBase;

hook_eventbus_subscribe('core', 'pre-object-hook-call', function(ObjectHookCall $ohc) {
    
    if (is_a($ohc->getObject(), ServiceBase::class)) {
        $con = \core\db\DatabaseHandler::getInstance()->getConnection('default');
        
        $arguments = $ohc->getArguments();
        
        // Form? => auto set lock for object
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
    }
    
});

hook_eventbus_subscribe('core', 'post-object-hook-call', function(ObjectHookCall $ohc) {
    $con = \core\db\DatabaseHandler::getInstance()->getConnection('default');
    
    $con->commitTransaction();
    
    // release locks, if set
    $con->releaseLocks();

});

