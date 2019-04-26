<?php



use core\ObjectContainer;
use core\event\CallbackPeopleEventListener;
use core\event\EventBus;
use core\Context;

Context::getInstance()->enableModule('webmail');

$eb = ObjectContainer::getInstance()->get(EventBus::class);

$eb->subscribe('masterdata', 'menu', new CallbackPeopleEventListener(function($evt) {
    $src = $evt->getSource();
    $src->addItem('E-mail', 'Identiteiten',  '/?m=webmail&c=identity');
    $src->addItem('E-mail', 'Templates',     '/?m=webmail&c=template');
    
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

