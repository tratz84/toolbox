<?php


use filesync\cron\WopiCleanupJob;


hook_eventbus_subscribe('croncontainer', 'init', function(\core\container\CronContainer $cronContainer) {
    $cronContainer->addCronjob( new WopiCleanupJob() );
});



