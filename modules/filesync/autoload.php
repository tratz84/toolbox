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
    $menuFilesync->setIconLabelUrl('fa-file', 'Filesync', '/?m=filesync&c=store', 115);
    $src->add($menuFilesync);
    
    
    $menuPdf = new Menu();
    $menuPdf->setIconLabelUrl('fa-file', 'PDF creator', '/?m=filesync&c=pagequeue&a=pdf', 20);
    $menuFilesync->addChildMenu( $menuPdf );
    
//     $menuPq = new Menu();
//     $menuPq->setIconLabelUrl('fa-file', 'Pagequeue', '/?m=filesync&c=pagequeue', 20);
//     $menuFilesync->addChildMenu( $menuPq );
}));


$eb->subscribe('masterdata', 'menu', new CallbackPeopleEventListener(function($evt) {
    $ctx = Context::getInstance();
    
    $src = $evt->getSource();
    
    $src->addItem('Filesync', 'Pagequeue instellingen',     '/?m=filesync&c=pagequeueSettings');
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



$eb->subscribe('base', 'dashboard', new CallbackPeopleEventListener(function($evt) {
    $dashboardWidgets = $evt->getSource();
    
    $ctx = Context::getInstance();
    
    $dashboardWidgets->addWidget('filesync-archive-upload'
                                    , 'Filesync: Archive file'
                                    , 'Bestanden archiveren'
                                    , '/?m=filesync&c=dashboard/archiveWidget');
    
}));


