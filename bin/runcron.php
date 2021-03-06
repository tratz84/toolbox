#!/usr/bin/env php
<?php

/**
 * bin/runcron.php - run's crontab-jobs for ALL environments/cusotmers
 * 
 */

use admin\service\AdminCustomerService;
use core\ObjectContainer;

include dirname(__FILE__).'/../config/config.php';


\core\Context::getInstance()->enableModule('admin');

// standalone?
if (is_standalone_installation()) {
    bootstrapCli( 'default' );
    
    print "Cron mode: Standalone\n";
    $cronService = object_container_get(\base\service\CronService::class);
    
    $cronService->runCron();
}
// multi-installation? => loop all through customers
else {
    $ics = ObjectContainer::getInstance()->get(AdminCustomerService::class);
    $customers = $ics->readCustomers();
    
    foreach($customers as $c) {
        $name = $c->getContextName();
        print "Customer: " . $name . "\n";
        get_url(BASE_URL . BASE_HREF . $name . '/?m=base&c=public/cron&a=run');
    }
}


print "Done\n";

// print BASE_URL . "\n";
