<?php


use core\ObjectContainer;
use core\event\CallbackPeopleEventListener;
use core\event\EventBus;
use core\event\PeopleEvent;
use core\Context;

Context::getInstance()->enableModule('filesync');

$eb = ObjectContainer::getInstance()->get(EventBus::class);


$eb->subscribe('base', 'company-edit-footer', new CallbackPeopleEventListener(function(PeopleEvent $evt) {
    $ftc = $evt->getSource();
    
    
    $html = get_component('filesync', 'archiveOverviewController', 'index', array('form' => $ftc->getSource()));
    if ($html) {
        $ftc->addTab('Documenten', $html, 50);
    }
}));



$eb->subscribe('base', 'person-edit-footer', new CallbackPeopleEventListener(function(PeopleEvent $evt) {
    $ftc = $evt->getSource();
    
    
    $html = get_component('filesync', 'archiveOverviewController', 'index', array('form' => $ftc->getSource()));
    if ($html) {
        $ftc->addTab('Documenten', $html, 50);
    }
}));
