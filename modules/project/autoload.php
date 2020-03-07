<?php




use core\ObjectContainer;
use core\event\CallbackPeopleEventListener;
use core\Context;

module_update_handler('project', '20200307');

Context::getInstance()->enableModule('project');


$oc = ObjectContainer::getInstance();

hook_register_javascript('project', BASE_HREF.'module/project/js/script.js');
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

