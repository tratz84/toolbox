<?php

namespace invoice\service;

use base\forms\FormChangesHtml;
use base\service\MetaService;
use base\util\ActivityUtil;
use core\ObjectContainer;
use core\exception\ObjectNotFoundException;
use core\forms\lists\ListResponse;
use core\service\ServiceBase;
use customer\service\CustomerService;
use invoice\InvoiceSettings;
use invoice\form\InvoiceForm;
use invoice\form\OfferForm;
use invoice\model\Invoice;
use invoice\model\InvoiceLine;
use invoice\model\InvoiceStatusDAO;
use invoice\model\Offer;
use invoice\model\OfferDAO;
use invoice\model\OfferLine;
use invoice\model\OfferLineDAO;
use invoice\model\OfferStatus;
use invoice\model\OfferStatusDAO;

class OfferService extends ServiceBase {
    

    public function __construct() {
        parent::__construct();
        
    }
    
    public function readAllOfferStatus() {
        $oDao = new OfferStatusDAO();
        return $oDao->readAll();
    }
    
    public function readActiveOfferStatus() {
        $oDao = new OfferStatusDAO();
        return $oDao->readActive();
    }
    
    public function readOfferStatus($id) {
        $oDao = new OfferStatusDAO();
        return $oDao->read($id);
    }

    public function saveOfferStatus($form) {
        $id = $form->getWidgetValue('offer_status_id');
        if ($id) {
            $offerStatus = $this->readOfferStatus($id);
        } else {
            $offerStatus = new OfferStatus();
        }
        
        $form->fill($offerStatus, array('offer_status_id', 'customer_id', 'description', 'default_selected', 'active'));
        
        if (!$offerStatus->save()) {
            return false;
        }
        
        if ($offerStatus->getDefaultSelected()) {
            $oDao = new OfferStatusDAO();
            $oDao->unsetDefaultSelected($offerStatus->getOfferStatusId());
        }
    }
    
    public function readDefaultOfferStatus() {
        $osDao = new OfferStatusDAO();
        
        $os = $osDao->readByDefaultStatus();
        if ($os)
            return $os;
        
        $os = $osDao->readFirst();
        return $os;
    }
    
    
    public function deleteOfferStatus($id) {
        // set offer status to null of currently used cases
        $oDao = new OfferDAO();
        $oDao->offerStatusToNull($id);
        
        $osDao = new OfferStatusDAO();
        $osDao->delete($id);
    }
    
    
    public function searchOffer($start, $limit, $opts = array()) {
        $oDao = new OfferDAO();
        
        $cursor = $oDao->search($opts);
        
        $r = ListResponse::fillByCursor($start, $limit, $cursor, array('offer_id', 'offer_status_id', 'company_id', 'person_id', 'total_calculated_price', 'total_calculated_price_incl_vat', 'subject', 'comment', 'accepted', 'offer_date', 'edited', 'created', 'firstname', 'insert_lastname', 'lastname', 'company_name', 'offer_status_description', 'offerNumberText'));
        
        return $r;
        
    }
    
    public function readOffer($id) {
        $oDao = new OfferDAO();
        
        $offer = $oDao->read($id);
        
        if (!$offer)
            return null;
        
        $olDao = new OfferLineDAO();
        $lines = $olDao->readByOffer($id);
        $offer->setOfferLines( $lines );
        
        $customerService = ObjectContainer::getInstance()->get(CustomerService::class);
        $customer = $customerService->readCustomerAuto($offer->getCompanyId(), $offer->getPersonId());
        $offer->setCustomer($customer);         // might be null
        
        return $offer;
    }
    
    
    public function readDefaultInvoiceStatus() {
        $isDao = new InvoiceStatusDAO();
        
        $is = $isDao->readByDefaultStatus();
        if ($is)
            return $is;
        
        $is = $isDao->readFirst();
        return $is;
    }
    
    
    
    public function saveOffer($form) {
        $id = $form->getWidgetValue('offer_id');
        if ($id) {
            $offer = $this->readOffer($id);
        } else {
            $offer = new Offer();
        }
        
        $isNew = $offer->isNew();
        
        if ($isNew) {
            $fch = FormChangesHtml::formNew($form);
        } else {
            $oldForm = new OfferForm();
            $oldForm->bind($offer);
            
            $fch = FormChangesHtml::formChanged($oldForm, $form);
        }
        
        // no changes? => skip (saveOffer is called when printing or sending email)
        if ($fch->hasChanges() == false) {
            return $offer;
        }
        
        $form->fill($offer, array('offer_id', 'customer_id', 'offer_status_id', 'offer_date', 'deposit', 'payment_upfront', 'subject', 'comment', 'note'));
        
        if ($offer->getOfferStatusId() == 0)
            $offer->setOfferStatusId(null);
        
        
        $totalCalculatedAmount = 0;
        $totalCalculatedAmountInclVat = 0;
        $newOfferLines = $form->getWidget('offerLines')->getObjects();
        for($x=0; $x < count($newOfferLines); $x++) {
            if (isset($newOfferLines[$x]['price'])) {
                $price = strtodouble( $newOfferLines[$x]['price'] );
                $vatAmount = myround( $price * strtodouble($newOfferLines[$x]['amount']) * $newOfferLines[$x]['vat'] / 100, 2 );
                
                $totalCalculatedAmount += myround( $price * $newOfferLines[$x]['amount'], 2 );
                $totalCalculatedAmountInclVat += myround( $price * $newOfferLines[$x]['amount'], 2 ) + $vatAmount;
                
                $newOfferLines[$x]['price'] = $price;
                $newOfferLines[$x]['vat_amount'] = $vatAmount;
            }
        }
        
        $offer->setTotalCalculatedPrice( myround($totalCalculatedAmount, 2) );
        $offer->setTotalCalculatedPriceInclVat( $totalCalculatedAmountInclVat );
        
        
        
        if (!$offer->save()) {
            return false;
        }
        
        $form->getWidget('offer_id')->setValue($offer->getOfferId());
        
        $olDao = new OfferLineDAO();
        $newOfferLines = $form->getWidget('offerLines')->getObjects();
        $olDao->mergeFormListMTO1('offer_id', $offer->getOfferId(), $newOfferLines);
        
        
        if ($isNew) {
            ActivityUtil::logActivity($offer->getCompanyId(), $offer->getPersonId(), 'invoice__offer', $offer->getOfferId(), 'offer-created', 'Offerte aangemaakt '.$offer->getOfferNumberText(), $fch->getHtml());
        } else {
            ActivityUtil::logActivity($offer->getCompanyId(), $offer->getPersonId(), 'invoice__offer', $offer->getOfferId(), 'offer-edited', 'Offerte aangepast '.$offer->getOfferNumberText(), $fch->getHtml());
        }
    }
    
    
    
    public function updateOfferStatus($offerId, $offerStatusId) {
        $osNew = $this->readOfferStatus($offerStatusId);
        if ($osNew == null)
            throw new ObjectNotFoundException('OfferStatus not found');
            
        $offer = $this->readOffer($offerId);
        if ($offer == null)
            throw new ObjectNotFoundException('Offer not found');
        
        $osOld = $this->readOfferStatus($offer->getOfferStatusId());
        
        
        $oDao = new OfferDAO();
        $oDao->updateStatus($offerId, $offerStatusId);
        
        $html = FormChangesHtml::tableFromArray([
            ['label' => 'Status', 'old' => $osOld?$osOld->getDescription():'', 'new' => $osNew->getDescription()]
        ]);
        
        
        ActivityUtil::logActivity($offer->getCompanyId(), $offer->getPersonId(), 'invoice__offer', $offer->getOfferId(), 'offer-update-status', 'Offerte status '.$offer->getOfferNumberText() . ': ' . $osNew->getDescription(), $html);
    }
    
    public function deleteOffer($offerId) {
        $offerId = (int)$offerId;
        
        $offer = $this->readOffer($offerId);
        
        // track changes
        $offerForm = new OfferForm();
        $offerForm->bind( $offer );
        $fch = FormChangesHtml::formDeleted($offerForm);
        
        // delete lines & offer
        $olDao = new OfferLineDAO();
        $olDao->deleteByOffer($offerId);
        
        $oDao = new OfferDAO();
        $oDao->delete($offerId);
        
        // log activity
        ActivityUtil::logActivity($offer->getCompanyId(), $offer->getPersonId(), 'invoice__offer', $offer->getOfferId(), 'offer-deleted', 'Offerte verwijderd '.$offer->getOfferNumberText(), $fch->getHtml());
    }
    
    
    public function updateOfferStatusSort($offerStatusIds) {
        $osDao = new OfferStatusDAO();
        $osDao->updateSort($offerStatusIds);
    }
    
    
    public function createPdf($offerId) {
        $offer = $this->readOffer($offerId);
        
        $invoiceSettings = $this->oc->get(InvoiceSettings::class);
        
//         $offerPdf = $this->oc->create( \context\ptw\pdf\LandscapeOfferPdf::class );
        $offerPdf = @$this->oc->create( $invoiceSettings->getOfferPdfClass() );
            
        
        $offerPdf->setOffer($offer);
        $offerPdf->render();
        
        return $offerPdf;
    }
    
    public function createInvoice($offerId) {
        $offer = $this->readOffer($offerId);
        
        $i = new Invoice();
        $i->setPersonId($offer->getPersonId());
        $i->setCompanyId($offer->getCompanyId());
        $i->setSubject($offer->getSubject());
        $i->setComment($offer->getComment());
        $i->setInvoiceDate(date('Y-m-d'));
        
        $defaultInvoiceStatus = $this->readDefaultInvoiceStatus();
        if ($defaultInvoiceStatus) {
            $i->setInvoiceStatusId( $defaultInvoiceStatus->getInvoiceStatusId() );
        }
        
        
        $ols = $offer->getOfferLines();
        $invoiceLines = array();
        for($x=0; $x < count($ols); $x++) {
            /**
             * @var OfferLine $ol
             */
            $ol = $ols[$x];
            
            $il = new InvoiceLine();
            $il->setArticleId($ol->getArticleId());
            if ($ol->getShortDescription2()) {
                $il->setShortDescription($ol->getShortDescription() . ': ' . $ol->getShortDescription2());
            } else {
                $il->setShortDescription($ol->getShortDescription());
            }
            $il->setAmount($ol->getAmount());
            
            $price = myround($ol->getPrice(), 2);
            $il->setPrice($price );
            
            $il->setVatPercentage($ol->getVat());
            
            $vatAmount = myround($price * $ol->getAmount() * $ol->getVat()/100, 2);
            $il->setVatAmount($vatAmount);
            
            $il->setInvoiceId($i->getInvoiceId());
            $invoiceLines[] = $il->getFields();
        }
        
        $i->setInvoiceLines($invoiceLines);
        
        $if = new InvoiceForm();
        $if->bind($i);
        
        $invoiceService = $this->oc->get(InvoiceService::class);
        $i = $invoiceService->saveInvoice( $if );
        
        $metaService = $this->oc->get(MetaService::class);
        $metaService->saveMeta(Invoice::class, $i->getInvoiceId(), 'offer_id', $offer->getOfferId());
        
        return $i;
    }
    
    
}

