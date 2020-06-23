<?php


hook_eventbus_subscribe('core', 'authorization-check', function($ac) {

    if ($ac->getModule() == 'base') {
        if ($ac->getController() == 'dashboard' && $ac->getAction() == 'index') {
            $ac->allowPermission();
        }
        if ($ac->getController() == 'js/dynamicscripts') {
            $ac->allowPermission();
        }
    }
    
});
