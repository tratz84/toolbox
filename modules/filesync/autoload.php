<?php

use base\model\Menu;
use core\Context;
use core\ObjectContainer;
use core\event\CallbackPeopleEventListener;
use core\event\EventBus;
use core\event\PeopleEvent;

Context::getInstance()->enableModule('filesync');

require_once __DIR__.'/lib/functions/misc.php';

module_update_handler('filesync', '20200416');

$eb = ObjectContainer::getInstance()->get(EventBus::class);

// access to filesync?
$jsStr = '';
if (hasCapability('core', 'userType.user')) {
    $jsStr = '?pqicon=1';
}
hook_register_javascript('filesync', BASE_HREF.'module/filesync/js/script.js'.$jsStr);
hook_htmlscriptloader_enableGroup('filesync');



// $arr[] = array('menu_code' => 'filesync',        'sort' => 1600, 'visible' => 1, 'icon' => 'fa-file',      'label' => 'Filesync',       'url' => '/?m=filesync&c=store');
$eb->subscribe('base', 'MenuService::listMainMenu', new CallbackPeopleEventListener(function($evt) {
    // permissions?
    if (hasCapability('core', 'userType.user') == false) {
        return;
    }
    
    $src = $evt->getSource();
    
    $menuFilesync = new Menu();
    $menuFilesync->setIconLabelUrl('fa-file', t('File archive'), '/?m=filesync&c=store', 115);
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



$eb->subscribe('customer', 'company-edit-footer', new CallbackPeopleEventListener(function(PeopleEvent $evt) {
    $ftc = $evt->getSource();
    
    
    $html = get_component('filesync', 'archiveOverviewController', 'index', array('form' => $ftc->getSource()));
    if ($html) {
        $ftc->addTab('Documenten', $html, 50);
    }
}));



$eb->subscribe('customer', 'person-edit-footer', new CallbackPeopleEventListener(function(PeopleEvent $evt) {
    $ftc = $evt->getSource();
    
    
    $html = get_component('filesync', 'archiveOverviewController', 'index', array('form' => $ftc->getSource()));
    if ($html) {
        $ftc->addTab('Documenten', $html, 50);
    }
}));



$eb->subscribe('base', 'dashboard', new CallbackPeopleEventListener(function($evt) {
    if (hasCapability('core', 'userType.user') == false) {
        return;
    }
    
    $dashboardWidgets = $evt->getSource();
    
    $ctx = Context::getInstance();
    
    $dashboardWidgets->addWidget('filesync-archive-upload'
                                    , 'Filesync: Archive file'
                                    , 'Bestanden archiveren'
                                    , '/?m=filesync&c=dashboard/archiveWidget');
    
}));

/**
 * Webmail action-button for importing e-mail attachments or e-mail itself
 */
$eb->subscribe('webmail', 'mailbox-mailactions', new CallbackPeopleEventListener(function($evt) {
    /** @var \core\container\ActionContainer $actionContainer */
    $actionContainer = $evt->getSource();
    
    $url = json_encode( appUrl('/?m=filesync&c=hooks/mailbox') );
    
    $onclick = 'show_popup('.$url.', { data: { email_id: selectedMailId } }  );';
    
    $actionContainer->addItem('filesync-import-file', '<input type="button" value="Archive" onclick="'.esc_attr($onclick).'" />', 100);
    
}));


// list of extra form-widgets for codegen-module
add_filter('form-generator-form-widgets', function($formWidgets) {
    
    $formWidgets[] = array(
        'type' => 'widget',
        'class' => \filesync\form\FilesyncSelectField::class,
        'label' => 'FilesyncSelectField'
    );
    
    
    return $formWidgets;
});



