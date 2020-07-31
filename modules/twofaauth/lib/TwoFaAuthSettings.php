<?php

namespace twofaauth;

class TwoFaAuthSettings {
    
    
    public function __construct() {
        
    }
    
    
    public function getEnabled() {
        return ctx()->getSetting('twofaauth__enabled', false);
    }
    
    public function getAuthMethod() {
        return ctx()->getSetting('twofaauth__auth_method', 'email');
    }
    
}


