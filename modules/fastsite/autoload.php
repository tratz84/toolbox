<?php


use core\ObjectContainer;
use core\event\CallbackPeopleEventListener;
use core\event\EventBus;
use core\event\PeopleEvent;
use core\Context;
use base\model\Menu;

Context::getInstance()->enableModule('fastsite');

$eb = ObjectContainer::getInstance()->get(EventBus::class);


$eb->subscribe('base', 'MenuService::listMainMenu', new CallbackPeopleEventListener(function($evt) {
    
    $ac = $evt->getSource();
    
    $mi1 = new Menu();
    $mi1->setIconLabelUrl('fa-file-archive-o', 'Templates', '/?m=fastsite&c=template');
    
    $ac->add($mi1);
}));

