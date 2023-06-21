<?php

namespace base\cron;


use core\cron\CronJobBase;
use base\model\CronRunDAO;
use base\service\CronService;

class CleanUpCron extends CronJobBase {
    
    public function __construct() {
        
        $this->daily = true;
        
    }
    
    
    public function run() {
        
        // delete old base__cron_run-records
        $cronService = object_container_get( CronService::class );
        $cronService->cleanupCronRun();
        
    }

    
    
}

