<?php

use base\model\Menu;
use core\Context;
use core\ObjectContainer;
use core\event\CallbackPeopleEventListener;
use core\event\EventBus;
use core\event\PeopleEvent;

Context::getInstance()->enableModule('filesync');

require_once __DIR__.'/lib/functions/misc.php';

hook_loader(__DIR__.'/hook/');

module_update_handler('filesync', '20200827');

$eb = ObjectContainer::getInstance()->get(EventBus::class);

// access to filesync?
$jsStr = '';
if (hasCapability('core', 'userType.user')) {
    $jsStr = '?pqicon=1';
}
hook_register_javascript('filesync', BASE_HREF.'module/filesync/js/script.js'.$jsStr);
hook_htmlscriptloader_enableGroup('filesync');


hook_eventbus_subscribe('base', 'user-capabilities', function($ucc) {
    $opts = array();
    
    $ucc->addCapability('filesync', 'manager', t('manager'), t('Manage files'));
});



$eb->subscribe('customer', 'company-edit-footer', new CallbackPeopleEventListener(function(PeopleEvent $evt) {
    $ftc = $evt->getSource();
    
    if (hasCapability('filesync', 'manager') == false)
        return;
    
    $html = get_component('filesync', 'archiveOverviewController', 'index', array('form' => $ftc->getSource()));
    if ($html) {
        $ftc->addTab(t('Documents'), $html, 50);
    }
}));



$eb->subscribe('customer', 'person-edit-footer', new CallbackPeopleEventListener(function(PeopleEvent $evt) {
    $ftc = $evt->getSource();
    
    if (hasCapability('filesync', 'manager') == false)
        return;
    
    
    $html = get_component('filesync', 'archiveOverviewController', 'index', array('form' => $ftc->getSource()));
    if ($html) {
        $ftc->addTab(t('Documents'), $html, 50);
    }
}));



$eb->subscribe('base', 'dashboard', new CallbackPeopleEventListener(function($evt) {
    if (hasCapability('core', 'userType.user') == false) {
        return;
    }
    
    if (hasCapability('filesync', 'manager') == false)
        return;
    
    
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


hook_eventbus_subscribe('base', 'ServerInfoContainer', function(\base\util\ServerInfoContainer $sic) {
    if (filesync_lookup_libreoffice()) {
        $sic->addInfo('OpenOffice', 'Ok');
    }
    else {
        $sic->addInfo('OpenOffice', 'Not found', 'soffice executable not found');
    }
});


