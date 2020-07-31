<?php

namespace twofaauth;

class TwoFaAuthSettings {
    
    
    public function __construct() {
        
    }
    
    
    public function getEnabled() {
        return ctx()->getSetting('twofaauth__enabled', false);
    }
    
    public function getEnforceWhenNoMail() {
        return ctx()->getSetting('twofaauth__enforce_when_no_mail', '0');
    }
    
    public function getAuthMethod() {
        return ctx()->getSetting('twofaauth__auth_method', 'email');
    }
    
    
    
}


