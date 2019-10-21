<?php



use core\ObjectContainer;
use core\event\CallbackPeopleEventListener;
use core\event\EventBus;
use core\Context;

Context::getInstance()->enableModule('report');

$eventBus = ObjectContainer::getInstance()->get(EventBus::class);

$eventBus->subscribe('base', 'user-capabilities', new CallbackPeopleEventListener(function($evt) {
    $evt->getSource()->addCapability('report', 'show-reports', t('Reports'), t('Access to reports'));
}));

