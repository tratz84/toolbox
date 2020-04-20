<?php

use base\model\Menu;
use core\Context;
use core\ObjectContainer;
use core\event\CallbackPeopleEventListener;
use core\event\EventBus;
use invoice\model\Invoice;

Context::getInstance()->enableModule('payment');

require_once __DIR__.'/lib/functions/misc.php';

module_update_handler('payment', '20200419');


$eb = ObjectContainer::getInstance()->get(EventBus::class);


$eb->subscribe('masterdata', 'menu', new CallbackPeopleEventListener(function($evt) {
    $ctx = Context::getInstance();
    
    $src = $evt->getSource();
    
    $src->addItem('Betalingen', 'Betalingsmethoden',     '/?m=payment&c=paymentMethod');
    $src->addItem('Betalingen', 'Import instellingen',   '/?m=payment&c=import/settings');
}));


$eb->subscribe('base', 'MenuService::listMainMenu', new CallbackPeopleEventListener(function($evt) {
    $ctx = \core\Context::getInstance();
    $src = $evt->getSource();
    
    if (hasCapability('payment', 'edit-payments')) {
        
        $menuOverviewPayments = new Menu();
        $menuOverviewPayments->setIconLabelUrl('fa-list', t('Payments'), '/?m=payment&c=paymentOverview');
        $menuOverviewPayments->setWeight(37);
        $src->add( $menuOverviewPayments );
        
        
        $menuNewPayment = new Menu();
        $menuNewPayment->setIconLabelUrl('fa-money', t('New payment'), '/?m=payment&c=payment');
        $menuOverviewPayments->addChildMenu($menuNewPayment);
        
        
        if (hasCapability('payment', 'import-payments')) {
            $menuImportPayments = new Menu();
            $menuImportPayments->setIconLabelUrl('fa-download', t('Import Payments'), '/?m=payment&c=import');
            $menuOverviewPayments->addChildMenu( $menuImportPayments );
        }
    }
    else if (hasCapability('payment', 'overview-payments')) {
        $menuOverviewPayments = new Menu();
        $menuOverviewPayments->setIconLabelUrl('fa-list', t('Overview Payments'), '/?m=payment&c=paymentOverview');
        $menuNewPayment->addChildMenu( $menuOverviewPayments );
    }
    
}));




hook_eventbus_subscribe('invoice', 'invoice-edit', function($actionContainer) {
    // might happen on new offer
    if (!$actionContainer->getObjectId())
        return;
    
    $created = object_meta_get(Invoice::class, $actionContainer->getObjectId(), 'payment_created');
    
    if ($created) {
        $actionContainer->addItem('create-payment', '<a href="javascript:void(0);" disabled=disabled title="Reeds aangemaakt">Betaling aanmaken</a>');
    } else {
        $actionContainer->addItem('create-payment', '<a href="'.appUrl('/?m=payment&c=invoice&a=create_payment&invoice_id='.$actionContainer->getObjectId()).'">Betaling aanmaken</a>');
    }
});


hook_eventbus_subscribe('base', 'company-edit-footer', function($ftc) {
    $form = $ftc->getSource();
    
    $html = get_component('payment', 'tabOverviewController', 'index', array('companyId' => $form->getWidgetValue('company_id')));
    
    if ($html) {
        $ftc->addTab('Betalingen', $html);
    }
});


hook_eventbus_subscribe('base', 'person-edit-footer', function($ftc) {
    $form = $ftc->getSource();
    
    $html = get_component('payment', 'tabOverviewController', 'index', array('personId' => $form->getWidgetValue('person_id')));
    
    if ($html) {
        $ftc->addTab('Betalingen', $html);
    }
});
    

