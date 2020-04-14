<?php





use base\model\Menu;
use core\Context;
use core\ObjectContainer;
use core\event\CallbackPeopleEventListener;
use core\event\EventBus;
use calendar\CalendarSettings;

Context::getInstance()->enableModule('calendar');

module_update_handler('calendar', '20200414');

$eb = ObjectContainer::getInstance()->get(EventBus::class);

$eb->subscribe('masterdata', 'menu', new CallbackPeopleEventListener(function($evt) {
    $src = $evt->getSource();

    $src->addItem('Kalender', t('Settings'),  '/?m=calendar&c=settings');
    $src->addItem('Kalender', t('Calendars'), '/?m=calendar&c=calendar');
}));


//     if (hasCapability('calendar', 'edit-calendar')) {
//         $arr[] = array('menu_code' => 'calendar',        'sort' => 900, 'visible' => 1, 'icon' => 'fa-calendar',  'label' => 'Kalender',  'url' => '/?m=calendar&c=view');
//     }
    
$eb->subscribe('base', 'MenuService::listMainMenu', new CallbackPeopleEventListener(function($evt) {
    
    $calendarSettings = object_container_get(CalendarSettings::class);
    
    $calitemActionsEnabled = $calendarSettings->calendarItemActionsEnabled();
    
    $src = $evt->getSource();
    
    $menuCal = new Menu();
    $menuCal->setIconLabelUrl('fa-calendar', t('Calendar'), '/?m=calendar&c=view');
    $menuCal->setWeight(40);
    if ($calitemActionsEnabled) {
        $menuCal->setMenuAsFirstChild( true );
    }
    $src->add($menuCal);
    
    if ($calitemActionsEnabled) {
        $menuActionOverview = new Menu();
        $menuActionOverview->setIconLabelUrl('fa-calendar', t('Actions'), '/?m=calendar&c=calitemActionOverview');
        $menuActionOverview->setWeight(10);
        $menuCal->addChildMenu( $menuActionOverview );
    }
    
}));

    
$eb->subscribe('base', 'dashboard', new CallbackPeopleEventListener(function($evt) {
    $dashboardWidgets = $evt->getSource();
    
    if (hasCapability('calendar', 'edit-calendar')) {
        /** @var CalendarSettings $calendarSettings */
        $calendarSettings = object_container_get( CalendarSettings::class );
        
        $dashboardWidgets->addWidget('upcoming-calendar-items', 'Kalender: Opkomende agenda punten', 'Opkomende agendapunten voor deze week', '/?m=calendar&c=dashboard&a=upcoming');
        
        if ($calendarSettings->calendarItemActionsEnabled()) {
            $dashboardWidgets->addWidget('calendar-items-actions', 'Kalender: Actiepunten agenda', 'Nog uit te voeren actiepunten agenda', '/?m=calendar&c=calitemActionOverview&a=dashboard');
        }
        
    }
}));
    
    
$eb->subscribe('base', 'user-capabilities', new CallbackPeopleEventListener(function($evt) {
    
    $evt->getSource()->addCapability('calendar', 'edit-calendar', 'Kalender', 'Bekijken + bewerken kalender');
    
}));

