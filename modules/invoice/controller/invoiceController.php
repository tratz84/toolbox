<?php



use core\container\ActionContainer;
use core\controller\BaseController;
use core\event\EventBus;
use core\exception\InvalidStateException;
use core\exception\ObjectNotFoundException;
use invoice\form\InvoiceForm;
use invoice\model\Invoice;
use invoice\pdf\LandscapeOfferPdf;
use invoice\service\InvoiceService;
use webmail\model\EmailTo;
use webmail\service\EmailService;
use webmail\service\EmailTemplateService;
use invoice\pdf\DefaultInvoicePdf;
use invoice\InvoiceSettings;

class invoiceController extends BaseController {
    
    public function init() {
        
        checkCapability('invoice', 'edit-invoice');
        
    }
    
    
    public function action_index() {
        
        $this->invoiceSettings = $this->oc->get(InvoiceSettings::class);
        
        $invoiceService = $this->oc->get(InvoiceService::class);
        
        $this->invoiceStatus = array();
        $this->invoiceStatus[] = array('value' => '', 'text' => 'Status');
        
        $invoiceStatus = $invoiceService->readActiveInvoiceStatus();
        foreach($invoiceStatus as $is) {
            $this->invoiceStatus[] = array(
                'value' => $is->getInvoiceStatusId(),
                'text' => $is->getDescription()
            );
        }
        
        $this->render();
    }
    
    
    public function action_edit() {
        $id = isset($_REQUEST['id'])?(int)$_REQUEST['id']:0;
        
        $invoiceService = $this->oc->get(InvoiceService::class);
        if ($id) {
            $invoice = $invoiceService->readInvoice($id);
            if ($invoice == null)
                return $this->renderError('Invoice not found');
        } else {
            $invoice = new Invoice();
        }
        
        // there must be atleast 1 active vat-tarif
        $vats = $invoiceService->readActiveVatTarifs();
        if (count($vats) == 0) {
            $this->errorMessage = 'Configureer eerst de btw percentages alvorens facturen aan te maken';
        }
        
        
        $invoiceForm = new InvoiceForm();
        $invoiceForm->bind($invoice);
        
        if (is_post()) {
            $invoiceForm->bind($_REQUEST);
            
            if ($invoiceForm->validate()) {
                $invoiceService->saveInvoice($invoiceForm);
                
                if (get_var('print')) {
                    $url = '/?m=invoice&c=invoice&a=print&id=' . $invoiceForm->getWidgetValue('invoice_id');
                } else if (get_var('sendmail')) {
                    $url = '/?m=invoice&c=invoice&a=sendmail&id=' . $invoiceForm->getWidgetValue('invoice_id');
                } else {
                    report_user_message('Wijzigingen opgeslagen');
                    
                    $url = '/?m=invoice&c=invoice&a=edit&id=' . $invoiceForm->getWidgetValue('invoice_id');
                }
                
                redirect($url);
            }
            
        }
        
        
        
        $this->isNew = $invoice->isNew();
        $this->form = $invoiceForm;
        
        
        $eb = $this->oc->get(EventBus::class);
        $this->actionContainer = new ActionContainer('invoice', $invoice->getInvoiceId());
        $eb->publishEvent($this->actionContainer, 'invoice', 'invoice-edit');
        
        
        $this->render();
    }
    
    
    public function action_update_status() {
        
        $invoiceService = $this->oc->get(InvoiceService::class);
        
        $invoiceService->updateInvoiceStatus($_REQUEST['invoice_id'], $_REQUEST['invoice_status_id']);
        
        $this->json(array(
            'status' => 'OK'
        ));
    }
    
    
    public function action_popup_status() {
        
        $invoiceService = $this->oc->get(InvoiceService::class);
        
        $this->invoiceStatus = $invoiceService->readActiveInvoiceStatus();
        
        $this->setShowDecorator(false);
        $this->render();
    }
    
    
    
    public function action_print() {
        $invoiceService = $this->oc->get(InvoiceService::class);
        $invoice = $invoiceService->readInvoice((int)$_REQUEST['id']);
        $invoicePdf = $invoiceService->createPdf((int)$_REQUEST['id']);
        
        $invoicePdf->Output('I', strOrder(1).'-'.$invoice->getInvoiceNumberText().'.pdf', true);
    }
    
    
    
    public function action_search() {
        $pageNo = isset($_REQUEST['pageNo']) ? (int)$_REQUEST['pageNo'] : 0;
        $limit = $this->ctx->getPageSize();
        
        $invoiceService = $this->oc->get(InvoiceService::class);
        
        $r = $invoiceService->searchInvoice($pageNo*$limit, $limit, $_REQUEST);
        
        $arr = array();
        $arr['listResponse'] = $r;
        
        
        $this->json($arr);
    }
    
    public function action_delete() {
        $invoiceService = $this->oc->get(InvoiceService::class);
        
        $i = $invoiceService->readInvoice($_REQUEST['id']);
        if (!$i) {
            throw new ObjectNotFoundException('Invoice not found');
        }
        
        $invoiceService->deleteInvoice($_REQUEST['id']);
        
        redirect('/?m=invoice&c=invoice');
    }
    
    
    public function action_customer_data() {
        
        if (strpos($_REQUEST['customerCode'], 'company-') === 0) {
            $companyId = substr($_REQUEST['customerCode'], strlen('company-'));
            
            include_component('base', 'company', 'widget', array('company_id' => $companyId));
        }
        
        if (strpos($_REQUEST['customerCode'], 'person-') === 0) {
            $personId = substr($_REQUEST['customerCode'], strlen('person-'));
            
            include_component('base', 'person', 'widget', array('person_id' => $personId));
        }
        
    }
    
    
    public function action_sendmail() {
        // create PDF
        $invoiceService = $this->oc->get(InvoiceService::class);
        $invoice = $invoiceService->readInvoice((int)$_REQUEST['id']);
        
        $invoicePdf = $this->oc->create(DefaultInvoicePdf::class);
        
        $invoicePdf->setInvoice($invoice);
        $invoicePdf->render();
        
        $rawPdfData = $invoicePdf->Output('S');
        
        // build template
        $emailTemplateService = $this->oc->get(EmailTemplateService::class);
        $template = $emailTemplateService->readByTemplateCode('INVOICE_MAIL');
        
        if ($template == null) {
            throw new InvalidStateException('INVOICE_MAIL-template not found');
        }
        
        $vars = array();
        $vars['naam'] = '';
        $vars = array_merge($invoice->getFields(), $vars);
        if ($invoice->getCustomer()) {
            if ($invoice->getCustomer()->getType() == 'company')
                $vars['naam'] = $invoice->getCustomer()->getCompany()->getContactPerson();
            else
                $vars['naam'] = $invoice->getCustomer()->getPersonName();
        }
        $vars['betreft'] = $invoice->getSubject();
        
        $html = $template->render($vars);
        $files = array();
        $files[] = array(
            'filename' => strOrder(1).'-'.$invoice->getInvoiceNumberText().'.pdf',
            'data' => $rawPdfData
        );
        
        // add uploaded attachments
        $invoiceAttachments = list_data_files('attachments/invoice/');
        foreach($invoiceAttachments as $iaFilename) {
            $path = get_data_file('attachments/invoice/'.$iaFilename);
            if ($path) {
                $files[] = array(
                    'filename' => $iaFilename,
                    'data' => file_get_contents($path)
                );
            }
        }
        
        
        
        $emailService = $this->oc->get(EmailService::class);
        $identity = $emailService->readFirstIdentity();
        
        $e = new \webmail\model\Email();
        $e->setCompanyId($invoice->getCompanyId());
        $e->setPersonId($invoice->getPersonId());
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
        
        
        $emailAddresses = $invoice->getCustomer()->getEmailList();
        if (count($emailAddresses) > 0) {
            $et = new EmailTo();
            $et->setToName( $vars['naam'] );
            $et->setToEmail( $emailAddresses[0]->getEmailAddress() );
            
            $e->addRecipient($et);
        }
        
        
        
        $emailService->createDraft($e, $files);
        
        
        redirect('/?m=webmail&c=view&id='.$e->getEmailId());
    }
    
}
