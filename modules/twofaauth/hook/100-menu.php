<?php


use core\event\CallbackPeopleEventListener;
use core\event\EventBus;

$eb = object_container_get( EventBus::class );


$eb->subscribe('masterdata', 'menu', new CallbackPeopleEventListener(function($evt) {
    $src = $evt->getSource();
    
    $src->addItem('2 Factor authentication', t('Settings'),  '/?m=twofaauth&c=settings');
}));


