#!/usr/bin/env php
<?php

/**
 * background_context.php - handles background processes & cronjobs for given context
 *
 */

if (count($argv) != 2) {
    print "Usage: {$argv[0]} <contextname>\n";
    exit;
}

// bootstrap
chdir(__DIR__.'/..');
include 'config/config.php';

$contextName = $argv[1];
bootstrapCli($contextName);

$lastCronRun = null;

while ( true ) {
    try {
        
        // publish event for starting any crons
        $ac = new \core\container\ArrayContainer();
        hook_eventbus_publish($ac, 'core', 'background-jobs');
        
        // TODO: check which crons has to be started/stopped
        var_export($ac->getItems());
        
        
        // run cron every 5 minuts
        if ($lastCronRun == null || $lastCronRun+(60*5) < time()) {
            print "CronService::runCron()\n";
            $cronService = object_container_get(\base\service\CronService::class);
            $cronService->runCron();
            
            $lastCronRun = time();
        }
    } catch (\Exception $ex) {
        // Exception.. close all database connections to reset state
        print "Error: " . $ex->getMessage() . "\n";
        \core\db\DatabaseHandler::getInstance()->closeAll();
    }
    
    sleep(5);
}
