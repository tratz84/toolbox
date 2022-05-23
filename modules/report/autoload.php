<?php



use core\ObjectContainer;
use core\event\CallbackPeopleEventListener;
use core\event\EventBus;
use core\Context;

Context::getInstance()->enableModule('report');

module_update_handler('report', '20220523');

hook_loader( __DIR__.'/hook' );



$eventBus = ObjectContainer::getInstance()->get(EventBus::class);

hook_eventbus_subscribe( 'base', 'user-capabilities', function($src) {
    $src->addCapability('report', 'show-reports', t('Reports'), t('Access to reports'));
});

