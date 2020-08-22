<?php

// $arr[] = array('menu_code' => 'filesync',        'sort' => 1600, 'visible' => 1, 'icon' => 'fa-file',      'label' => 'Filesync',       'url' => '/?m=filesync&c=store');

use base\model\Menu;
use core\Context;

hook_eventbus_subscribe('base', 'MenuService::listMainMenu', function($src) {
    // permissions?
    if (hasCapability('core', 'userType.user') == false) {
        return;
    }
    if (hasCapability('filesync', 'manager') == false)
        return;
        
    $menuFilesync = new Menu();
    $menuFilesync->setIconLabelUrl('fa-file', t('File archive'), '/?m=filesync&c=store', 115);
    $src->add($menuFilesync);
    
    
    $menuPdf = new Menu();
    $menuPdf->setIconLabelUrl('fa-file', 'PDF creator', '/?m=filesync&c=pagequeue&a=pdf', 20);
    $menuFilesync->addChildMenu( $menuPdf );
    
    //     $menuPq = new Menu();
    //     $menuPq->setIconLabelUrl('fa-file', 'Pagequeue', '/?m=filesync&c=pagequeue', 20);
    //     $menuFilesync->addChildMenu( $menuPq );
});


hook_eventbus_subscribe('masterdata', 'menu', function($src) {
    $ctx = Context::getInstance();
    
    $src->addItem('Filesync', t('Settings'),       '/?m=filesync&c=settings');
    $src->addItem('Filesync', t('File templates'), '/?m=filesync&c=filetemplates');
    
});

