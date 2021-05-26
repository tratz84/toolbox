<?php


use base\service\MetaService;
use core\container\ActionContainer;
use core\controller\BaseController;
use core\event\EventBus;
use core\exception\InvalidStateException;
use core\exception\ObjectNotFoundException;
use invoice\InvoiceSettings;
use invoice\form\OfferForm;
use invoice\model\Invoice;
use invoice\model\Offer;
use invoice\pdf\LandscapeOfferPdf;
use invoice\service\InvoiceService;
use invoice\service\OfferService;
use webmail\model\EmailTo;
use webmail\service\EmailService;
use webmail\service\EmailTemplateService;

class offerController extends BaseController {
    
    public function init() {
        $this->addTitle(t('Offers'));
    }
    
    public function action_index() {
        
        $this->invoiceSettings = $this->oc->get(InvoiceSettings::class);
        
        $offerService = $this->oc->get(OfferService::class);
        
        $this->offerStatus = array();
        $this->offerStatus[] = array('value' => '', 'text' => 'Status');
        
        $offerStatus = $offerService->readActiveOfferStatus();
        foreach($offerStatus as $os) {
            $this->offerStatus[] = array(
                'value' => $os->getOfferStatusId(),
                'text' => $os->getDescription()
            );
        }
        
        $this->render();
    }
    
    
    public function action_edit() {
        $id = isset($_REQUEST['id'])?(int)$_REQUEST['id']:0;
        
        $offerService = $this->oc->get(OfferService::class);
        if ($id) {
            $offer = $offerService->readOffer($id);
            if ($offer == null)
                return $this->renderError('Offer not found');
            
            $strTitle = t('Offer').' ' . $offer->getOfferNumberText();
            if ($offer->getCustomer()) {
                $strTitle .= ' - ' . $offer->getCustomer()->getName();
            }
            
            $this->addTitle( $strTitle );
        } else {
            $this->addTitle(t('New offer'));
            
            $offer = new Offer();
        }
        
        // there must be atleast 1 active vat-tarif
        $invoiceService = $this->oc->get(InvoiceService::class);
        $vats = $invoiceService->readActiveVatTarifs();
        if (count($vats) == 0) {
            $this->errorMessage = 'Configureer eerst de btw percentages alvorens offertes aan te maken';
        }
        
        
        $offerForm = new OfferForm();
        $offerForm->bind($offer);
        
        if (is_post()) {
            // locked & print? => skip saving
            if (dbobject_is_locked($offer) && get_var('print')) {
                $url = '/?m=invoice&c=offer&a=print&id=' . $offerForm->getWidgetValue('offer_id');
                redirect($url);
            }
            // locked & generate invoice?
            else if (dbobject_is_locked($offer) && get_var('generateInvoice')) {
                redirect('/?m=invoice&c=offer&a=generate_invoice&id=' . $offerForm->getWidgetValue('offer_id'));
            }
            // locked & sendmail?
            else if (dbobject_is_locked($offer) && get_var('sendmail')) {
                redirect('/?m=invoice&c=offer&a=sendmail&id=' . $offerForm->getWidgetValue('offer_id'));
            }
            
            check_dbobject_locked($offer);
            
            $offerForm->bind($_REQUEST);
            
            if ($offerForm->validate()) {
                $offerService->saveOffer($offerForm);
                
                if (get_var('print')) {
                    $url = '/?m=invoice&c=offer&a=print&id=' . $offerForm->getWidgetValue('offer_id');
                } else if (get_var('generateInvoice')) {
                    $url = '/?m=invoice&c=offer&a=generate_invoice&id=' . $offerForm->getWidgetValue('offer_id');
                } else if (get_var('sendmail')) {
                    $url = '/?m=invoice&c=offer&a=sendmail&id=' . $offerForm->getWidgetValue('offer_id');
                } else {
                    report_user_message('Wijzigingen opgeslagen');
                    
                    $url = '/?m=invoice&c=offer&a=edit&id=' . $offerForm->getWidgetValue('offer_id');
                }
                
                redirect($url);
            }
            
        }
        
        
        $this->invoiceId = null;
        if ($offer->isNew() == false) {
            $metaService = $this->oc->get(MetaService::class);
            $this->invoiceId = $metaService->getIdByObjectValue(Invoice::class, 'offer_id', $offer->getOfferId());
        }
        
        $this->isNew = $offer->isNew();
        $this->offer = $offer;
        $this->form = $offerForm;
        
        
        $eb = $this->oc->get(EventBus::class);
        $this->actionContainer = new ActionContainer('offer', $offer->getOfferId());
        
        $this->actionContainer->addItem('create-invoice', '<a href="javascript:void(0);" onclick="generateInvoice();">'.strOrder(1).' aanmaken</a>', 5);
        
        $eb->publishEvent($this->actionContainer, 'invoice', 'offer-edit');
        
        
        $this->render();
    }
    
    
    
    public function action_update_status() {
        $offerService = $this->oc->get(OfferService::class);
        
        $offerService->updateOfferStatus($_REQUEST['offer_id'], $_REQUEST['offer_status_id']);
        
        $this->json(array(
            'status' => 'OK'
        ));
    }
    
    
    public function action_popup_status() {
        $offerService = $this->oc->get(OfferService::class);
        
        $this->offerStatus = $offerService->readActiveOfferStatus();
        
        $this->setShowDecorator(false);
        $this->render();
    }
    
    
    public function action_print() {
        $offerService = $this->oc->get(OfferService::class);
        $offer = $offerService->readOffer((int)$_REQUEST['id']);
        $offerPdf = $offerService->createPdf((int)$_REQUEST['id']);
        
        @$offerPdf->Output('I', 'offerte-'.$offer->getOfferNumberText().'.pdf', true);
    }
    
 
    
    public function action_search() {
        $pageNo = isset($_REQUEST['pageNo']) ? (int)$_REQUEST['pageNo'] : 0;
        $limit = $this->ctx->getPageSize();
        
        $offerService = $this->oc->get(OfferService::class);
        
        $r = $offerService->searchOffer($pageNo*$limit, $limit, $_REQUEST);
        
        $arr = array();
        $arr['listResponse'] = $r;
        
        
        $this->json($arr);
    }

    public function action_delete() {
        $offerService = $this->oc->get(OfferService::class);
        
        $offerService->deleteOffer($_REQUEST['id']);
        
        redirect('/?m=invoice&c=offer');
    }
    
    
    public function action_customer_data() {
        
        if (strpos($_REQUEST['customerCode'], 'company-') === 0) {
            $companyId = substr($_REQUEST['customerCode'], strlen('company-'));
            
            include_component('customer', 'company', 'widget', array('company_id' => $companyId));
        }
        
        if (strpos($_REQUEST['customerCode'], 'person-') === 0) {
            $personId = substr($_REQUEST['customerCode'], strlen('person-'));
            
            include_component('customer', 'person', 'widget', array('person_id' => $personId));
        }
        
    }
    
    
    public function action_sendmail() {
        // create PDF
        $offerService = $this->oc->get(OfferService::class);
        $offer = $offerService->readOffer((int)$_REQUEST['id']);
        
        $offerPdf = $offerService->createPdf((int)$_REQUEST['id']);
        
        $rawPdfData = $offerPdf->Output('S');
        
        // build template
        $emailTemplateService = $this->oc->get(EmailTemplateService::class);
        $template = $emailTemplateService->readByTemplateCode('OFFERTE_MAIL');
        
        if ($template == null) {
            throw new InvalidStateException('OFFERTE_MAIL-template not found');
        }
        
        $vars = array();
        $vars['naam'] = '';
        $vars['betreft'] = $offer->getSubject();
        $vars['document_no'] = $offer->getOfferNumberText();
        $vars = array_merge($offer->getFields(), $vars);
        if ($offer->getCustomer()) {
            if ($offer->getCustomer()->getType() == 'company')
                $vars['naam'] = $offer->getCustomer()->getCompany()->getContactPerson();
            else
                $vars['naam'] = $offer->getCustomer()->getPersonName();
        }
        
        $html = $template->render($vars);
        $files = array();
        $files[] = array(
            'filename' => 'offerte-'.$offer->getOfferNumberText().'.pdf',
            'data' => $rawPdfData
        );
        
        // add uploaded attachments
        $offerAttachments = list_data_files('attachments/offer/');
        foreach($offerAttachments as $oaFilename) {
            $path = get_data_file('attachments/offer/'.$oaFilename);
            if ($path) {
                $files[] = array(
                    'filename' => $oaFilename,
                    'data' => file_get_contents($path)
                );
            }
        }
        
        
        
        $emailService = $this->oc->get(EmailService::class);
        $identity = $emailService->readFirstIdentity();
        
        $e = new \webmail\model\Email();
        $e->setCompanyId($offer->getCompanyId());
        $e->setPersonId($offer->getPersonId());
        $e->setStatus(\webmail\model\Email::STATUS_DRAFT);
        if ($identity) {
            $e->setIdentityId($identity->getIdentityId());
            $e->setFromName($identity->getFromName());
            $e->setFromEmail($identity->getFromEmail());
        }
        $e->setUserId($this->ctx->getUser()->getUserId());
        $subject = apply_html_vars($template->getSubject(), $vars);
        $e->setSubject($subject);
        $e->setTextContent($html);
        $e->setIncoming(false);
        
        
        $emailAddresses = $offer->getCustomer()->getEmailList();
        if (count($emailAddresses) > 0) {
            $et = new EmailTo();
            $et->setToName( $vars['naam'] );
            $et->setToEmail( $emailAddresses[0]->getEmailAddress() );
            
            $e->addRecipient($et);
        }
        
        
        $templateTos = $template->getTemplateTos();
        foreach($templateTos as $tt) {
            if (validate_email($tt->getToEmail()) == false)
                continue;
            
            $et = new EmailTo();
            $et->setToType( $tt->getToType() );
            $et->setToName( $tt->getToName() );
            $et->setToEmail( $tt->getToEmail() );
            
            $e->addRecipient($et);
        }
        
        
        
        $emailService->createDraft($e, $files);
        
        
        redirect('/?m=webmail&c=view&id='.$e->getEmailId());
    }
    
    
    public function action_generate_invoice() {
        
        $offerService = $this->oc->get(OfferService::class);
        $invoice = $offerService->createInvoice((int)get_var('id'));
        
        redirect('/?m=invoice&c=invoice&a=edit&id=' . $invoice->getInvoiceId());
    }
    
    
}


