<?php

use base\model\Menu;
use core\Context;
use core\ObjectContainer;
use core\event\CallbackPeopleEventListener;
use core\event\EventBus;

Context::getInstance()->enableModule('payment');

$eb = ObjectContainer::getInstance()->get(EventBus::class);


$eb->subscribe('masterdata', 'menu', new CallbackPeopleEventListener(function($evt) {
    $ctx = Context::getInstance();
    
    $src = $evt->getSource();
    
    $src->addItem('Betalingen', 'Betalingsmethoden',     '/?m=payment&c=paymentMethod');
}));


$eb->subscribe('base', 'MenuService::listMainMenu', new CallbackPeopleEventListener(function($evt) {
    $ctx = \core\Context::getInstance();
    $src = $evt->getSource();
    
    if (hasCapability('payment', 'edit-payments')) {
        $menuNewPayment = new Menu();
        $menuNewPayment->setSubmenuLabel(t('New payment'));
        $menuNewPayment->setIconLabelUrl('fa-money', t('Payments'), '/?m=payment&c=add');
        $menuNewPayment->setWeight(37);
        $src->add($menuNewPayment);
        
        $menuOverviewPayments = new Menu();
        $menuOverviewPayments->setIconLabelUrl('fa-list', t('Overview Payments'), '/?m=payment&c=overview');
        $menuNewPayment->addChildMenu( $menuOverviewPayments );
        
        if (hasCapability('payment', 'import-payments')) {
            $menuImportPayments = new Menu();
            $menuImportPayments->setIconLabelUrl('fa-download', t('Import Payments'), '/?m=import&c=import');
            $menuNewPayment->addChildMenu( $menuImportPayments );
        }
    }
    else if (hasCapability('payment', 'overview-payments')) {
        $menuOverviewPayments = new Menu();
        $menuOverviewPayments->setIconLabelUrl('fa-list', t('Overview Payments'), '/?m=payment&c=overview');
        $menuNewPayment->addChildMenu( $menuOverviewPayments );
    }
    
}));



