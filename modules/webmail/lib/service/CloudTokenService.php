<?php


namespace webmail\service;


use core\service\ServiceBase;
use webmail\model\WebmailAzureTokenDAO;

class CloudTokenService extends ServiceBase {
    
    
    
    public function readAzureTokens() {
        
        $watDao = new WebmailAzureTokenDAO();
        
        return $watDao->readAll();
    }
    
    
}




