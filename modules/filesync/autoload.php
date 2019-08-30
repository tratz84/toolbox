<?php


use base\model\Menu;
use core\Context;
use core\ObjectContainer;
use core\event\CallbackPeopleEventListener;
use core\event\EventBus;
use core\event\PeopleEvent;

Context::getInstance()->enableModule('filesync');

$eb = ObjectContainer::getInstance()->get(EventBus::class);


hook_register_javascript('filesync', BASE_HREF.'module/filesync/js/script.js');
hook_htmlscriptloader_enableGroup('filesync');



// $arr[] = array('menu_code' => 'filesync',        'sort' => 1600, 'visible' => 1, 'icon' => 'fa-file',      'label' => 'Filesync',       'url' => '/?m=filesync&c=store');
$eb->subscribe('base', 'MenuService::listMainMenu', new CallbackPeopleEventListener(function($evt) {
    $src = $evt->getSource();
    
    $menuFilesync = new Menu();
    $menuFilesync->setIconLabelUrl('fa-file', 'Filesync', '/?m=filesync&c=store', 145);
    $src->add($menuFilesync);
    
    
    $menuPq = new Menu();
    $menuPq->setIconLabelUrl('fa-file', 'Pagequeue', '/?m=filesync&c=pagequeue', 20);
    $menuFilesync->addChildMenu( $menuPq );
    
}));



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
