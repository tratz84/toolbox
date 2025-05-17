<?php



hook_eventbus_subscribe('croncontainer', 'init', function($cronContainer) {
    
    $cronContainer->addCronjob( new \mollie\cron\MollieCheckStatusCron() );
    
   
});


