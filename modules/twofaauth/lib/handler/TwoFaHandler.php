<?php

namespace twofaauth\handler;


use twofaauth\TwoFaAuthSettings;
use twofaauth\service\TwoFaService;

class TwoFaHandler {
    
    
    public function __construct() {
        
    }
    
    
    public function execute() {
        $faSettings = object_container_get( TwoFaAuthSettings::class );
        
        // not enabled? => skip
        if ($faSettings->getEnabled() == false) {
            return;
        }
        
        if (get_var('c') == 'js/dynamicscripts') {
            return;
        }
        
        // not yet authenticated? => skip
        if (ctx()->getUser() == null) {
            return;
        }
        
        $tfService = object_container_get(TwoFaService::class);
        
        // check cookie
        if (isset($_COOKIE['twofaauth'])) {
            $cookie = $tfService->readCookie( $_COOKIE['twofaauth'] );
            if ($cookie && $cookie->getActivated()) {
                return true;
            }
        }
        
        
        
        // handle 2-fa auth
        $authMethod = $faSettings->getAuthMethod();
        if ($authMethod == 'email') {
            $tfeh = new TwoFaEmailHandler();
            $tfeh->execute();
        }
        
    }
    
}

