<?php



use core\Context;
use core\event\CapabilityEvent;
use core\exception\AuthorizationException;

/**
 * returns true or false
 */
function hasCapability($module, $capabilityCode=null) {
    $ctx = Context::getInstance();
    
    // module disabled? => always return false
    if ($ctx->isModuleEnabled($module) == false)
        return false;
    
    $user = $ctx->getUser();

    if (!$user) return false;

    if ($user->getUserType() == 'admin')
        return true;
    
    // publish capability event
    $cc = new CapabilityEvent($module, $capabilityCode);
    hook_eventbus_publish($cc, 'core', 'has-capability');
    if ($cc->hasResult()) {
        return $cc->getResult();
    }
    
    return $user->hasCapability($module, $capabilityCode);
}

/**
 * throws Exception
 */
function checkCapability($module, $capabilityCode) {
    if (hasCapability($module, $capabilityCode) == false) {
        throw new AuthorizationException('No authorization to requested module');
    }
}

