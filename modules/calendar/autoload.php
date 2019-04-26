<?php





use core\ObjectContainer;
use core\event\CallbackPeopleEventListener;
use core\event\EventBus;
use core\Context;

Context::getInstance()->enableModule('calendar');

$eb = ObjectContainer::getInstance()->get(EventBus::class);

$eb->subscribe('masterdata', 'menu', new CallbackPeopleEventListener(function($evt) {
    $src = $evt->getSource();

    $src->addItem('Kalender', 'Kalenders',     '/?m=calendar&c=calendar');
}));

    
$eb->subscribe('base', 'dashboard', new CallbackPeopleEventListener(function($evt) {
    $dashboardWidgets = $evt->getSource();
    
    if (hasCapability('calendar', 'edit-calendar')) {
        $dashboardWidgets->addWidget('upcoming-calendar-items', 'Kalender: Opkomende agenda punten', 'Opkomende agendapunten voor deze week', '/?m=calendar&c=dashboard&a=upcoming');
    }
}));
    
    
$eb->subscribe('base', 'user-capabilities', new CallbackPeopleEventListener(function($evt) {
    
    $evt->getSource()->addCapability('calendar', 'edit-calendar', 'Kalender', 'Bekijken + bewerken kalender');
    
}));

