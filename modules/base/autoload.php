<?php


require_once dirname(__FILE__).'/lib/functions/person.php';
require_once dirname(__FILE__).'/lib/functions/misc.php';
require_once dirname(__FILE__).'/lib/functions/user.php';
require_once dirname(__FILE__).'/lib/functions/object_meta.php';
require_once dirname(__FILE__).'/lib/functions/object_lock.php';


use core\Context;
use core\ObjectContainer;
use core\event\CallbackPeopleEventListener;
use core\event\EventBus;
use core\event\PeopleEvent;

Context::getInstance()->enableModule('base');

module_update_handler('base', '20200504');

hook_loader(__DIR__.'/hook');

hook_register_javascript('select-person-list-edit', '/module/base/js/select-person-edit-list.js');
hook_register_javascript('select-company-list-edit', '/module/base/js/select-company-edit-list.js');


$eb = ObjectContainer::getInstance()->get(EventBus::class);

$eb->subscribe('report', 'menu-list', new CallbackPeopleEventListener(function($evt) {
    /**
     * report\ReportMenuList
     */
    $reportMenuList = $evt->getSource();
    
    $reportMenuList->addMenuItem('Algemeen - Maandoverzicht', 'base', 'report/summaryPerMonthController');
    
    $reportMenuList->addMenuItem('Klantenoverzicht', 'base', 'report/customerReportController', '/?m=base&c=report/customerReport&a=xls');
    
    if (hasCapability('base', 'list-activity')) {
        $reportMenuList->addMenuItem(t('Event viewer'), 'base', 'report/activityReportController');
    }
}));


$eb->subscribe('masterdata', 'menu', new CallbackPeopleEventListener(function($evt) {
    $src = $evt->getSource();
//     $src->addItem('Klanten', 'Bedrijfsoorten',     '/?m=base&c=masterdata/companyType');

    $src->addItem(t('Settings'), t('Scheduled tasks'),     '/?m=base&c=cronList');
}));


$eb->subscribe('base', 'dashboard', new CallbackPeopleEventListener(function($evt) {
    $dashboardWidgets = $evt->getSource();
    
    if (hasCapability('base', 'list-activity')) {
        $dashboardWidgets->addWidget('log-activity', t('General: Last event viewer items'), t('Last 100 activities event viewer'), '/?m=base&c=dashboardWidgets&a=logActivity');
    }
}));
    

    
$eb->subscribe('base', 'user-capabilities', new CallbackPeopleEventListener(function($evt) {
    // with masterdata permission, user can give itself admin-rights :s
//     $evt->getSource()->addCapability('base', 'edit-masterdata', 'Stamgegevens', 'Toegang tot stamgegevenspagina');
    
    $evt->getSource()->addCapability('base', 'list-activity', t('Event viewer'), t('Show events system'));
}));



$eb->subscribe('base', 'company-edit-footer', new CallbackPeopleEventListener(function(PeopleEvent $evt) {
    $ftc = $evt->getSource();
    
    $companyId = $evt->getSource()->getSource()->getWidgetValue('company_id');
    if (!$companyId)
        return;

    
    $html = get_component('base', 'notes/notestab', 'index', array('company_id' => $companyId));
    if ($html) {
        $ftc->addTab('Notities', $html, 9);
    }
    
    
    if (hasCapability('base', 'list-activity')) {
        $html = get_component('base', 'activityOverview', 'index', array('companyId' => $companyId));
        if ($html) {
            $ftc->addTab('Logboek', $html, 100);
        }
    }
}));
    
    
$eb->subscribe('base', 'person-edit-footer', new CallbackPeopleEventListener(function(PeopleEvent $evt) {
    $ftc = $evt->getSource();
    
    $personId = $evt->getSource()->getSource()->getWidgetValue('person_id');
    if (!$personId)
        return;

    $html = get_component('base', 'notes/notestab', 'index', array('person_id' => $personId));
    if ($html) {
        $ftc->addTab('Notities', $html, 9);
    }
    
    
    if (hasCapability('base', 'list-activity')) {
        $html = get_component('base', 'activityOverview', 'index', array('personId' => $personId));
        
        if ($html) {
            $ftc->addTab('Logboek', $html, 100);
        }
    }
}));


// customers handles as one? => redirect links to company-/person-overview to customer-overview
if (ctx()->isCustomersSplit() == false) {
    add_filter('appUrl', function($url) {
        if (endsWith($url, '/?m=base&c=company') || endsWith($url, '/?m=base&c=person')) {
            return appUrl('/?m=base&c=customer');
        }
        
        return $url;
    });
}




