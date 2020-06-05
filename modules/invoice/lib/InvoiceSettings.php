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
    
    /**
     * invoiceLocked() - business rule for checking if invoice must be locked
     *  TODO: when an accounting-module is added, invoices must be locked after it's
     *        booked or vat-reporting is done
     */
    public function invoiceLocked(\invoice\model\Invoice $invoice) {
        // only invoices are locked
        if ($this->getOrderType() != 'invoice') {
            return false;
        }
        
        $invoice_date = $invoice->getInvoiceDate();
        
        // invalid date?
        if (valid_date($invoice_date) == false) {
            return false;
        }
        
        $locked = apply_filter('invoice-locked', false, ['invoice' => $invoice]);
        
        if ($locked) {
            return true;
        }
        
        // invoice older then 90 days? => auto-lock
//         $days = days_between( $invoice_date, date('Y-m-d') );
//         if ($days > 90) {
//             return true;
//         }
        
        return false;
    }
    
    
    public function getBillableEnabled() {
        $ctx = Context::getInstance();
        return $ctx->getSetting('invoice__billable_enabled', false);
    }
    
    public function getBillableOnlyOpen() {
        $ctx = Context::getInstance();
        return $ctx->getSetting('invoice__billable_only_open', false);
    }
    
    
}

