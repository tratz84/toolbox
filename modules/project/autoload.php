<?php




use core\Context;
use core\ObjectContainer;
use core\event\CallbackPeopleEventListener;
use core\event\PeopleEvent;

module_update_handler('project', '20200316');

Context::getInstance()->enableModule('project');


$oc = ObjectContainer::getInstance();

// access to project?
$jsStr = '';
if (hasCapability('core', 'userType.user')) {
    $jsStr = '?phicon=1';
}
hook_register_javascript('project', BASE_HREF.'module/project/js/script.js'.$jsStr);
hook_htmlscriptloader_enableGroup('project');


$eb = $oc->get(\core\event\EventBus::class);

$eb->subscribe('masterdata', 'menu', new CallbackPeopleEventListener(function($evt) {
    $src = $evt->getSource();
    $src->addItem('Projecten', 'Uurstatus',     '/?m=project&c=projectHourStatus');
    $src->addItem('Projecten', 'Uursoorten',     '/?m=project&c=projectHourType');
    
}));



$eb->subscribe('report', 'menu-list', new CallbackPeopleEventListener(function($evt) {
    /**
     * report\ReportMenuList
     */
    $reportMenuList = $evt->getSource();
    
    $reportMenuList->addMenuItem('Projecten - overzicht',   'project', 'report/hours', '/?m=project&c=report/hours&a=xls');
}));

$eb->subscribe('base', 'report-summaryPerMonth', new CallbackPeopleEventListener(function($evt) {
    $datasources = $evt->getSource();
    
    $datasources->add([
        'label' => 'Project uren',
        'url' => '/?m=project&c=report/summaryPerMonth'
    ]);
    
}));



    
    
$eb->subscribe('customer', 'company-edit-footer', new CallbackPeopleEventListener(function(PeopleEvent $evt) {
    $ftc = $evt->getSource();
    
    $companyId = $evt->getSource()->getSource()->getWidgetValue('company_id');
    if (!$companyId)
        return;
    
    
    $html = get_component('project', 'projectTab', 'index', array('companyId' => $companyId));
    if ($html) {
        $ftc->addTab(t('Project hours'), $html, 9.2);
    }
        
}));
    

$eb->subscribe('customer', 'person-edit-footer', new CallbackPeopleEventListener(function(PeopleEvent $evt) {
    $ftc = $evt->getSource();
    
    $personId = $evt->getSource()->getSource()->getWidgetValue('person_id');
    if (!$personId)
        return;
    
    $html = get_component('project', 'projectTab', 'index', array('personId' => $personId));
    if ($html) {
        $ftc->addTab(t('Project hours'), $html, 9.2);
    }
        
}));
    
    

