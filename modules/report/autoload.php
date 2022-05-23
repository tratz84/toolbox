<?php


require_once __DIR__.'/lib/function/misc.php';


use core\ObjectContainer;
use core\event\CallbackPeopleEventListener;
use core\event\EventBus;
use core\Context;

Context::getInstance()->enableModule('report');

module_update_handler('report', '20220524');

hook_loader( __DIR__.'/hook' );



hook_eventbus_subscribe( 'base', 'user-capabilities', function($src) {
    $src->addCapability('report', 'show-reports', t('Reports'), t('Access to reports'));
});

