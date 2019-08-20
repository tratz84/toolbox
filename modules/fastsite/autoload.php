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
    
    $miWebpage = new Menu();
    $miWebpage->setIconLabelUrl('fa-file-archive-o', 'Webpages', '/?m=fastsite&c=webpage');
    $ac->add($miWebpage);
    
    $miMenu = new Menu();
    $miMenu->setIconLabelUrl('fa-file-archive-o', 'Webmenu', '/?m=fastsite&c=webmenu');
    $ac->add($miMenu);
    
    $miTemplates = new Menu();
    $miTemplates->setIconLabelUrl('fa-file-archive-o', 'Templates', '/?m=fastsite&c=template');
    $ac->add($miTemplates);
    
}));

