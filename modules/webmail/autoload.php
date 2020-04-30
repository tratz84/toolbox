<?php



use base\model\Menu;
use core\Context;
use core\ObjectContainer;
use core\container\ArrayContainer;
use core\container\CronContainer;
use core\event\CallbackPeopleEventListener;
use core\event\EventBus;
use core\event\PeopleEvent;
use webmail\cron\WebmailSyncJob;
use webmail\service\ConnectorService;

require_once __DIR__.'/lib/functions/misc.php';


Context::getInstance()->enableModule('webmail');

// core\db\mysql\MysqlTableGenerator::updateModule('webmail', true);
// die('done');

module_update_handler('webmail', '20200404');

hook_register_javascript('webmail', '/module/webmail/js/script.js');

$eb = ObjectContainer::getInstance()->get(EventBus::class);

$eb->subscribe('masterdata', 'menu', new CallbackPeopleEventListener(function($evt) {
    $src = $evt->getSource();
    $src->addItem('E-mail', t('Settings'),          '/?m=webmail&c=settings');
    $src->addItem('E-mail', t('Identities'),        '/?m=webmail&c=identity');
    $src->addItem('E-mail', t('Templates'),         '/?m=webmail&c=template');
    $src->addItem('E-mail', t('Mail server (out)'), '/?m=webmail&c=settingsMailOut');
    
    $ctx = Context::getInstance();
    
    if ($ctx->isExperimental()) {
        $src->addItem('E-mail', 'Connectors',     '/?m=webmail&c=connector');
        $src->addItem('E-mail', 'Filters',        '/?m=webmail&c=filter');
        $src->addItem('E-mail', t('Maintenance'), '/?m=webmail&c=maintenance/index');
    }
}));


$eb->subscribe('base', 'user-capabilities', new CallbackPeopleEventListener(function($evt) {
    $evt->getSource()->addCapability('webmail', 'create-mail', 'E-mail aanmaken', 'E-mails aanmaken en klaarzetten als concept');
    $evt->getSource()->addCapability('webmail', 'send-mail', 'Verstuur e-mail', 'E-mails versturen');
    
}));


$eb->subscribe('base', 'MenuService::listMainMenu', new CallbackPeopleEventListener(function($evt) {
    
    /** @var \core\Context $ctx */
    $ctx = \core\Context::getInstance();
    
    /** @var ArrayContainer $menuContainer */
    $menuContainer = $evt->getSource();
    
    if (hasCapability('webmail', 'send-mail')) {
        // active connectors? => show Webmail first
        if (ctx()->getSetting('webmail__active_connector_count', 0) > 0) {
            $m = new Menu();
            $m->setIconLabelUrl('fa-send', 'Webmail', '/?m=webmail&c=mailbox/search');
            $m->setWeight(70);
            
            $menu_outbox = new Menu();
            $menu_outbox->setIconLabelUrl('fa-send', 'Outbox', '/?m=webmail&c=email');
            $m->addChildMenu( $menu_outbox );
            
            $menuContainer->add($m);
        }
        // No active connectors? => show Outbox first & E-mail is called E-mail archive
        else {
            $m = new Menu();
            $m->setIconLabelUrl('fa-send', 'E-mail', '/?m=webmail&c=email');
            $m->setWeight(70);
            if ($ctx->isExperimental()) {
                $menu_mailbox = new Menu();
                $menu_mailbox->setIconLabelUrl('fa-send', 'E-mail archive', '/?m=webmail&c=mailbox/search');
                $m->addChildMenu($menu_mailbox);
            }
            $menuContainer->add($m);
        }
    }
}));


    
$eb->subscribe('base', 'company-edit-footer', new CallbackPeopleEventListener(function(PeopleEvent $evt) {
    if (!hasCapability('webmail', 'send-mail'))
        return;
    
    /** @var ConnectorService $connectorService */
    $connectorService = object_container_get(ConnectorService::class);
    if ($connectorService->hasConnectors() == false)
        return;

    $ftc = $evt->getSource();
    
    $companyId = $ftc->getSource()->getWidgetValue('company_id');
    
    // new Company? => skip
    if (!$companyId)
        return;
    
    $webmailHtml = get_component('webmail', 'mailbox/tabController', 'index', array('companyId' => $companyId));
    $ftc->addTab('Mail', $webmailHtml, 80);
}));


$eb->subscribe('base', 'person-edit-footer', new CallbackPeopleEventListener(function(PeopleEvent $evt) {
    if (!hasCapability('webmail', 'send-mail'))
        return;
    
    /** @var ConnectorService $connectorService */
    $connectorService = object_container_get(ConnectorService::class);
    if ($connectorService->hasConnectors() == false)
        return;
        
    $ftc = $evt->getSource();
    
    $personId = $ftc->getSource()->getWidgetValue('person_id');
    
    // new Person? => skip
    if (!$personId)
        return;
    
    $webmailHtml = get_component('webmail', 'mailbox/tabController', 'index', array('personId' => $personId));
    $ftc->addTab('Mail', $webmailHtml, 80);
}));
    

$eb->subscribe('base', 'dashboard', new CallbackPeopleEventListener(function($evt) {
    if (!hasCapability('core', 'userType.user'))
        return;
    
    $dashboardWidgets = $evt->getSource();
    
    $ctx = Context::getInstance();
    if (ctx()->isExperimental()) {
        $dashboardWidgets->addWidget('webmail-mailbox-widget', 'Webmail: Laatste e-mails', 'Overzicht van laatst binnengekomen e-mails', '/?m=webmail&c=mailbox/dashboard');
    }
}));


// webmail imap/pop3 sync
hook_eventbus_subscribe('croncontainer', 'init', function(CronContainer $cronContainer) {
    $cronContainer->addCronjob( new WebmailSyncJob() );
});


