<?php


namespace securemail\cron;


use core\cron\CronJobBase;
use securemail\service\SecureMailService;

class SecretMessageCron extends CronJobBase {
    
    public function __construct() {
        $this->daily = false;
        
    }
    
    
    
    public function run() {
        
        $smservice = object_container_get( SecureMailService::class );
        
        $smservice->deleteExpired();
        
    }
    
    
}

