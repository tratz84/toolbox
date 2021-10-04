<?php



ctx()->enableModule('reportbuilder');

module_update_handler('reportbuilder', 20210306);

// hook_loader(__DIR__.'/hook/');




hook_eventbus_subscribe('croncontainer', 'init', function($cronContainer) {
    
    $cronContainer->addCronjob( new \reportbuilder\cron\ReportbuilderCacheCron() );
    
});


