<?php

use twofaauth\cron\TwoFaCleanupJob;

hook_eventbus_subscribe('croncontainer', 'init', function(\core\container\CronContainer $cronContainer) {
    
    $cronContainer->addCronjob( new TwoFaCleanupJob() );
    
});
    
    
    
    