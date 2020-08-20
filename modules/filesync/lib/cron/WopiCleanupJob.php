<?php



namespace filesync\cron;


use core\cron\CronJobBase;
use filesync\service\WopiService;

class WopiCleanupJob extends CronJobBase {
    
    
    public function __construct() {
        $this->daily = true;
    }
    
    
    public function run() {
        $wopiService = object_container_get( WopiService::class );
        
        $wopiService->cleanupTokens();
    }
    
    public function getStatus() {
        return 'ok';
    }
    
    
}





