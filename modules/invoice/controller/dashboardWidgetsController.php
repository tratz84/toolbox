<?php


use base\service\MetaService;
use core\controller\BaseController;
use invoice\InvoiceSettings;
use invoice\form\RecentInvoiceWidgetForm;
use invoice\service\InvoiceService;
use invoice\service\OfferService;

class dashboardWidgetsController extends BaseController {
    
    public function action_lastOffers() {
        
        $offerService = $this->oc->get(OfferService::class);
        
        $this->offers = $offerService->searchOffer(0, 10, array());
        
        
        $this->setShowDecorator(false);
        
        $this->render();
    }

    public function action_lastChangedOffers() {
        
        $offerService = $this->oc->get(OfferService::class);
        
        $opts = array();
        $opts['order'] = 'edited desc';
        $this->offers = $offerService->searchOffer(0, 10, $opts);
        
        
        $this->setShowDecorator(false);
        
        $this->render();
    }
    
    
    public function action_lastInvoices() {
        $this->invoiceSettings = $this->oc->get(InvoiceSettings::class);
        
        $invoiceService = $this->oc->get(InvoiceService::class);
        
        $opts = array();
        $opts['order'] = 'edited desc';
        
        $userId = $this->ctx->getUser()->getUserId();
        $metaService = $this->oc->get(MetaService::class);
        
        $this->widgetSettings = @unserialize( $metaService->getMetaValue('user', $userId, 'dashboard_lastInvoices-widget') );
        if (is_array($this->widgetSettings) && isset($this->widgetSettings['invoiceStatusIds']))
            $opts['invoiceStatusIds'] = $this->widgetSettings['invoiceStatusIds'];
        
        $this->invoices = $invoiceService->searchInvoice(0, 100, $opts);
        
        
        $this->setShowDecorator(false);
        
        $this->render();
    }
    
    public function action_lastInvoices_settings() {
        
        $userId = $this->ctx->getUser()->getUserId();
        $metaService = $this->oc->get(MetaService::class);
        $widgetSettings = @unserialize( $metaService->getMetaValue('user', $userId, 'dashboard_lastInvoices-widget') );

        $this->form = new RecentInvoiceWidgetForm();
        $this->form->bind($widgetSettings);
        
        if (get_var('save')) {
            $this->form->bind($_REQUEST);
            
            $widgetSettings = array();
            
            // set invoiceStatusIds
            $widgetSettings['invoiceStatusIds'] = $this->form->getSelectedInvoiceStatusIds();
            
            $widgetSettings['show_open_days']      = $this->form->getWidgetValue('show_open_days')      ? true : false;
            $widgetSettings['show_invoice_amount'] = $this->form->getWidgetValue('show_invoice_amount') ? true : false;
            
            // save
            $metaService->saveMeta('user', $userId, 'dashboard_lastInvoices-widget', serialize($widgetSettings));
            
            $this->json(array('status' => 'OK'));
            exit;
        }
        
        $this->setShowDecorator(false);
        return $this->render();
        
    }
    
    
}


