<?php

namespace twofaauth\service;


use core\service\ServiceBase;
use twofaauth\model\TwoFaCookieDAO;

class TwoFaService extends ServiceBase {
    
    
    
    public function readCookie( $cookieValue) {
        $tfCookieDao = object_container_get( TwoFaCookieDAO::class );
        
        return $tfCookieDao->readByValue( $cookieValue );
    }
    
    
}
