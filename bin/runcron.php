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

$ics = ObjectContainer::getInstance()->get(AdminCustomerService::class);
$customers = $ics->readCustomers();


foreach($customers as $c) {
    $name = $c->getContextName();
    print "Customer: " . $name . "\n";
    get_url(BASE_URL . '/' . $name . '/?m=base&c=public/cron&a=run');
}

print "Done\n";

// print BASE_URL . "\n";
