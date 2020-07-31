<?php

namespace twofaauth\handler;


use twofaauth\TwoFaAuthSettings;
use twofaauth\service\TwoFaService;

class TwoFaHandler {
    
    
    public function __construct() {
        
    }
    
    
    public function execute() {
        $faSettings = object_container_get( TwoFaAuthSettings::class );
        
        // autologin used? => skip 2fa
        if (isset($_SESSION['admin_autologin']) && $_SESSION['admin_autologin']) {
            return;
        }
        
        // not enabled? => skip
        if ($faSettings->getEnabled() == false) {
            return;
        }

        // not yet authenticated? => skip
        if (ctx()->getUser() == null) {
            return;
        }
        
        // no valid e-mailadres?
        if ($faSettings->getEnforceWhenNoMail() == false && validate_email(ctx()->getUser()->getEmail()) == false) {
            return;
        }
        
        // hmz, this might be handled differently..
        if (get_var('c') == 'js/dynamicscripts') {
            return;
        }
        
        // check cookie
        $tfService = object_container_get(TwoFaService::class);
        if (isset($_COOKIE['twofaauth']) && $tfService->checkCookie( $_COOKIE['twofaauth'] )) {
            return true;
        }
        
        // handle 2-fa auth
        $authMethod = $faSettings->getAuthMethod();
        if ($authMethod == 'email') {
            $tfeh = new TwoFaEmailHandler();
            $tfeh->execute();
        }
        
    }
    
}

