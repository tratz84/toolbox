<?php


use core\event\CapabilityEvent;
use base\service\UserService;

if (ctx()->isUserGroupsEnabled()) {
    hook_eventbus_subscribe('core', 'has-capability', function(CapabilityEvent $cc) {
        
        // fetch apabilities
        $user = $cc->getUser();
        $userService = object_container_get( UserService::class );
        
        $map = $userService->mapGroupCapabilitiesForUser( $user->getUserId() );
        
        $c = $cc->getModuleName() . '.' . $cc->getCapabilityCode();
        
        // check if user has capability
        if (isset($map[$c]))
            $cc->setResult( true );
        
    });
}

