<?php


use core\Context;
use core\event\CapabilityEvent;
use core\exception\AuthorizationException;

/**
 * returns true or false
 */
function hasCapability($module, $capabilityCode=null) {
    $user = ctx()->getUser();

    if (!$user) return false;

    
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


function allowCapability( $module, $capabilityCode ) {
    ctx()->getUser()->allowCapability( $module, $capabilityCode );
}





