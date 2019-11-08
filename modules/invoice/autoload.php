<?php


use base\forms\CompanyForm;
use base\model\Menu;
use core\Context;
use core\ObjectContainer;
use core\event\CallbackPeopleEventListener;
use core\event\EventBus;
use core\event\PeopleEvent;
use invoice\InvoiceSettings;
use invoice\model\CompanySetting;
use invoice\model\Invoice;
use invoice\model\Offer;
use invoice\service\InvoiceService;
use invoice\service\OfferService;

Context::getInstance()->enableModule('invoice');

$eb = ObjectContainer::getInstance()->get(EventBus::class);


$eb->subscribe('masterdata', 'menu', new CallbackPeopleEventListener(function($evt) {
    $ctx = Context::getInstance();
    
    $src = $evt->getSource();
    
    if ($ctx->getSetting('offerModuleEnabled')) {
        $src->addItem('Facturatie', 'Offerte statussen',     '/?m=invoice&c=offerStatus');
    }
    
    if ($ctx->getSetting('invoiceModuleEnabled')) {
        $src->addItem('Facturatie', strOrder(1).' statussen',     '/?m=invoice&c=invoiceStatus');
    }
    
    if ($ctx->getSetting('offerModuleEnabled') || $ctx->getSetting('invoiceModuleEnabled')) {
        $src->addItem('Facturatie', 'Btw %',     '/?m=invoice&c=vat');
    }
    
    if ($ctx->getSetting('invoiceModuleEnabled') || $ctx->getSetting('offerModuleEnabled')) {
        $src->addItem('Facturatie', 'Instellingen',     '/?m=invoice&c=settings');
        
        $src->addItem('Facturatie', 'Betalingsmogelijkheden',     '/?m=invoice&c=paymentMethod');
    }
    
    $src->addItem('Artikelen', 'Artikelen',     '/?m=invoice&c=article');
    $src->addItem('Artikelen', 'Artikelgroepen',     '/?m=invoice&c=articleGroup');
}));

$eb->subscribe('base', 'dashboard', new CallbackPeopleEventListener(function($evt) {
    $dashboardWidgets = $evt->getSource();
    
    $ctx = Context::getInstance();
    if ($ctx->getSetting('offerModuleEnabled')) {
        $dashboardWidgets->addWidget('invoice-recent-offers', 'Offerte: Laatst toegevoegde offertes', 'Overzicht van recent toegevoegde offertes', '/?m=invoice&c=dashboardWidgets&a=lastOffers');
        $dashboardWidgets->addWidget('invoice-recent-changed-offers', 'Offerte: Laatst gewijzigde offertes', 'Overzicht van recent gewijzigde offertes', '/?m=invoice&c=dashboardWidgets&a=lastChangedOffers');
    }
    
    if ($ctx->getSetting('invoiceModuleEnabled')) {
        $dashboardWidgets->addWidget('invoice-recent-invoices', strOrder(3).': Laatste '.strtolower(strOrder(2)), 'Overzicht recent aangemaakte '.strtolower(strOrder(2)), '/?m=invoice&c=dashboardWidgets&a=lastInvoices');
    }
}));

    
$eb->subscribe('base', 'MenuService::listMainMenu', new CallbackPeopleEventListener(function($evt) {
    $ctx = \core\Context::getInstance();
    $src = $evt->getSource();
    
    if (hasCapability('invoice', 'edit-offer')) {
        $menuOffers = new Menu();
        $menuOffers->setIconLabelUrl('fa-share-alt', 'Offertes', '/?m=invoice&c=offer');
        $menuOffers->setWeight(35);
        $src->add($menuOffers);
    }

    if (hasCapability('invoice', 'edit-invoice')) {
        $menuInvoice = new Menu();
        $menuInvoice->setIconLabelUrl('fa-file-archive-o', strOrder(3), '/?m=invoice&c=invoice');
        $menuInvoice->setWeight(36);
        $src->add($menuInvoice);
    }

    if ($ctx->isExperimental()) {
        $menuBillable = new Menu();
        $menuBillable->setIconLabelUrl('fa-money', 'Billable', '/?m=invoice&c=tobill');
        $menuBillable->setWeight(37);
        $src->add($menuBillable);
    }
    
}));


$eb->subscribe('base', 'company-edit-footer', new CallbackPeopleEventListener(function(PeopleEvent $evt) {
    $ftc = $evt->getSource();
    
    if (hasCapability('invoice', 'edit-invoice')) {
        $companyId = $ftc->getSource()->getWidgetValue('company_id');
        $orderHtml = get_component('invoice', 'invoiceOverviewController', 'index', array('companyId' => $companyId));
        if ($orderHtml) {
            $ftc->addTab(strOrder(2), $orderHtml, 20);
        }
    }
    
    $offerHtml = get_component('invoice', 'offerOverviewController', 'index', array('form' => $ftc->getSource()));
    if ($offerHtml) {
        $ftc->addTab('Offertes', $offerHtml, 30);
    }
}));
    
    
$eb->subscribe('base', 'person-edit-footer', new CallbackPeopleEventListener(function(PeopleEvent $evt) {
    $ftc = $evt->getSource();
    
    if (hasCapability('invoice', 'edit-invoice')) {
        $personId = $ftc->getSource()->getWidgetValue('person_id');
        $orderHtml = get_component('invoice', 'invoiceOverviewController', 'index', array('personId' => $personId));
        if ($orderHtml) {
            $ftc->addTab(strOrder(2), $orderHtml, 20);
        }
    }
    
    $offerHtml = get_component('invoice', 'offerOverviewController', 'index', array('form' => $ftc->getSource()));
    if ($offerHtml) {
        $ftc->addTab('Offertes', $offerHtml, 30);
    }
}));



$invoiceSettings = ObjectContainer::getInstance()->get(InvoiceSettings::class);


if ($invoiceSettings->getIntracommunautair()) {

    // add tax_excemption-field
    hook_create_object(CompanyForm::class, function(CompanyForm $form) {
        $w = new \core\forms\CheckboxField('tax_shift', '', 'Intracommunautair');
        $w->setInfoText('Diensten/producten worden intracommunautaire geleverd? (buitenlandsbedrijf)');
        $w->setPrio(76);
        $form->addWidget($w);
    });
    
    $eb->subscribe('core', 'post-call-base\\service\\CompanyService::readCompany', new CallbackPeopleEventListener(function(PeopleEvent $evt) {
        $ohc = $evt->getSource();
        $company = $ohc->getReturnValue();
        
        if ($company->getCompanyId()) {
            $invoiceService = ObjectContainer::getInstance()->get(InvoiceService::class);
            $cs = $invoiceService->readCompanySettings($company->getCompanyId());
            if ($cs && $cs->getTaxShift()) {
                $company->setField('tax_shift', true);
            } else {
                $company->setField('tax_shift', false);
            }
        }
    }));
    // handle saveCompany
    $eb->subscribe('core', 'post-call-base\\service\\CompanyService::save', new CallbackPeopleEventListener(function(PeopleEvent $evt) {
        $ohc = $evt->getSource();
        $arguments = $ohc->getArguments();
        
        $companyId = $ohc->getReturnValue();
        
        if ($companyId) {
            $invoiceService = ObjectContainer::getInstance()->get(InvoiceService::class);
            $cs = $invoiceService->readCompanySettings($companyId);
            if ($cs == null) {
                $cs = new CompanySetting();
                $cs->setCompanyId($companyId);
            }
            
            $t = $arguments[0]->getWidgetValue('tax_shift');
            $cs->setTaxShift( $t ? 1 : 0 );
            $invoiceService->saveCompanySettings($cs);
        }
    }));
        
        
}



$eb->subscribe('report', 'menu-list', new CallbackPeopleEventListener(function($evt) {
    $ctx = Context::getInstance();
    
    /**
     * report\ReportMenuList
     */
    $reportMenuList = $evt->getSource();
    
    if ($ctx->getSetting('invoiceModuleEnabled')) {
        $reportMenuList->addMenuItem(strOrder(3) . ' - totalen',   'invoice', 'report/invoiceTotals');
    }
}));
    


$eb->subscribe('core', 'lookupobject', new CallbackPeopleEventListener(function($evt) {
    /**
     * @var LookupObject $lookupObject
     */
    $lookupObject = $evt->getSource();
    
    if ($lookupObject->getObjectName() == Offer::class) {
        $offerService = object_container_get(OfferService::class);
        
        $offer = $offerService->readOffer( $lookupObject->getId() );
        
        $lookupObject->setObject( $offer );
    }
    
    if ($lookupObject->getObjectName() == Invoice::class) {
        $invoiceService = object_container_get(InvoiceService::class);
        
        $invoice = $invoiceService->readInvoice( $lookupObject->getId() );
        
        $lookupObject->setObject( $invoice );
    }
    
}));

$eb->subscribe('base', 'report-summaryPerMonth', new CallbackPeopleEventListener(function($evt) {
    $datasources = $evt->getSource();
    
    $datasources->add([
        'label' => 'Factuur bedragen',
        'url' => '/?m=invoice&c=report/summaryPerMonth'
    ]);
    
}));

