<?php


use base\model\Menu;
use core\Context;
use core\ObjectContainer;
use core\event\CallbackPeopleEventListener;
use core\event\EventBus;

Context::getInstance()->enableModule('docqueue');

$eb = ObjectContainer::getInstance()->get(EventBus::class);



$eb->subscribe('base', 'MenuService::listMainMenu', new CallbackPeopleEventListener(function($evt) {
    
    $ac = $evt->getSource();
    
    $miDocs = new Menu();
    $miDocs->setIconLabelUrl('fa-file-archive-o', 'Document queue', '/?m=docqueue&c=list');
    $ac->add($miDocs);
    
}));
    
