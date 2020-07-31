<?php

namespace twofaauth\cron;


use core\cron\CronJobBase;
use twofaauth\service\TwoFaService;

class TwoFaCleanupJob extends CronJobBase {
    
    public function __construct() {
        $this->daily = true;
    }
    
    
    public function run() {
        $tfService = object_container_get( TwoFaService::class );
        
        $tfService->cleanupCookies();
    }
    
    public function getStatus() {
        return 'ok';
    }
    
}

