<?php



use core\ObjectContainer;
use core\event\CallbackPeopleEventListener;
use core\event\EventBus;
use core\Context;
use core\container\ArrayContainer;
use base\model\Menu;

Context::getInstance()->enableModule('webmail');

module_update_handler('webmail', '20200319');

// core\db\mysql\MysqlTableGenerator::updateModule('webmail', true);
// exit;

$eb = ObjectContainer::getInstance()->get(EventBus::class);

$eb->subscribe('masterdata', 'menu', new CallbackPeopleEventListener(function($evt) {
    $src = $evt->getSource();
    $src->addItem('E-mail', t('Identities'),        '/?m=webmail&c=identity');
    $src->addItem('E-mail', t('Templates'),         '/?m=webmail&c=template');
    $src->addItem('E-mail', t('Mail server (out)'), '/?m=webmail&c=settingsMailOut');
    
    $ctx = Context::getInstance();
    
    if ($ctx->isExperimental()) {
        $src->addItem('E-mail', 'Connectors',  '/?m=webmail&c=connector');
        $src->addItem('E-mail', 'Filters',     '/?m=webmail&c=filter');
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
        $m = new Menu();
        $m->setIconLabelUrl('fa-send', 'E-mail', '/?m=webmail&c=email');
        $m->setWeight(70);
        
        if ($ctx->isExperimental()) {
            $menu_mailbox = new Menu();
            $menu_mailbox->setIconLabelUrl('fa-send', 'Webmail', '/?m=webmail&c=mailbox/search');
            $m->addChildMenu($menu_mailbox);
        }
        
        $menuContainer->add($m);
    }
    
        
}));




