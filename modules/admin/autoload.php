<?php


use core\Context;
use core\ObjectContainer;
use core\event\CallbackPeopleEventListener;
use core\event\EventBus;

if (is_standalone_installation()) {
    
    Context::getInstance()->enableModule('admin');
    
    $eb = ObjectContainer::getInstance()->get(EventBus::class);
    
    $eb->subscribe('masterdata', 'menu', new CallbackPeopleEventListener(function($evt) {
        $ctx = Context::getInstance();
        
        $src = $evt->getSource();
        
        $src->addItem('Admin', 'Error log', '/?m=admin&c=exception');
        $src->setSectionPrio('Admin', 20);
    }));
        
    
}

