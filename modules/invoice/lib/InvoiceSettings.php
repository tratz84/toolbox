<?php


namespace invoice;


use invoice\pdf\DefaultInvoicePdf;
use invoice\pdf\LandscapeOfferPdf;
use invoice\pdf\DefaultOfferPdf;
use core\Context;

class InvoiceSettings {
    
    public function __construct() {
        
    }


    public function getOrderType() {
        $ctx = Context::getInstance();
        $ot = $ctx->getSetting('invoice__orderType');

        if (!$ot) {
            return 'order';
        }

        return $ot;
    }

    
    public function getIntracommunautair() {
        $ctx = Context::getInstance();
        return $ctx->getSetting('invoice__intracommunautaire', false);
    }
    
    public function getPricesIncVat() {
        $ctx = Context::getInstance();
        return $ctx->getSetting('invoice__prices_inc_vat', false);
    }
    
    
    public function getOfferPdfTemplates() {
        $t = array();
        
        $t[ DefaultOfferPdf::class ] = 'Standaard opmaak';
        $t[ LandscapeOfferPdf::class ] = 'Rood liggend';
        
        return $t;
    }
    
    public function getInvoicePdfTemplates() {
        $t = array();
        
        $t[ DefaultInvoicePdf::class ] = 'Standaard opmaak';
        
        return $t;
    }
    
    public function getInvoicePdfClass() {
        $tpls = $this->getInvoicePdfTemplates();
        
        // check if template exists
        $ctx = Context::getInstance();
        $s = $ctx->getSetting('invoice__invoiceTemplate');
        if (isset($tpls[$s])) {
            return $s;
        }
        
        // return first
        $keys = array_keys($tpls);
        return $keys[0];
    }
    
    public function getOfferPdfClass() {
        $tpls = $this->getOfferPdfTemplates();
        
        // check if template exists
        $ctx = Context::getInstance();
        $s = $ctx->getSetting('invoice__offerTemplate');
        if (isset($tpls[$s])) {
            return $s;
        }
        
        // return first
        $keys = array_keys($tpls);
        return $keys[0];
    }
    
}

