<?php




use base\cron\CleanUpCron;

hook_eventbus_subscribe('croncontainer', 'init', function($cronContainer) {
    /** @var \core\container\CronContainer $cronContainer */
    
    $cronContainer->addCronjob( new CleanUpCron() );
    
});


