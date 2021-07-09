<?php


use twofaauth\handler\TwoFaHandler;


hook_eventbus_subscribe('core', 'filter-executed', function($filter) {
    
    if (get_class($filter) == 'core\\filter\\AuthFilter') {
        $tfh = object_container_get( TwoFaHandler::class );
        $tfh->execute();
    }
    
});

