<?php



use core\container\ActionContainer;
use core\controller\BaseController;
use core\event\EventBus;
use core\exception\InvalidStateException;
use core\exception\ObjectNotFoundException;
use invoice\InvoiceSettings;
use invoice\form\InvoiceForm;
use invoice\model\Invoice;
use invoice\model\InvoiceLine;
use invoice\pdf\DefaultInvoicePdf;
use invoice\service\InvoiceService;
use webmail\model\EmailTo;
use webmail\service\EmailService;
use webmail\service\EmailTemplateService;

class invoiceController extends BaseController {
    
    public function init() {
        
        checkCapability('invoice', 'edit-invoice');
        
        $this->addTitle(strOrder(2));
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
        
        
        $this->actionContainer = new ActionContainer('invoice-index-actions', null);
        $this->actionContainer->addItem('update-status', '<a href="javascript:void(0);" id="btnChangeStatus">Status bijwerken</a>');
        hook_eventbus_publish($this->actionContainer, 'invoice', 'invoice-index-container');
        
        $this->render();
    }
    
    
    public function action_edit() {
        $id = isset($_REQUEST['id'])?(int)$_REQUEST['id']:0;
        
        $invoiceSettings = $this->oc->get(InvoiceSettings::class);

        $invoiceService = $this->oc->get(InvoiceService::class);
        if ($id) {
            $invoice = $invoiceService->readInvoice($id);
            if ($invoice == null)
                return $this->renderError('Invoice not found');
            
            $strTitle = strOrder('1') . ' ' . $invoice->getInvoiceNumberText();
            if ($invoice->getCustomer())
                $strTitle .= ' - ' . $invoice->getCustomer()->getName();
            $this->addTitle($strTitle);
        } else {
            $invoice = new Invoice();
            
            if (\core\Context::getInstance()->getSetting('invoice__orderType') == 'invoice') {
                $this->addTitle(t('New invoice'));
            } else {
                $this->addTitle(t('New order'));
            }
        }
        
        // there must be atleast 1 active vat-tarif
        $vats = $invoiceService->readActiveVatTarifs();
        if (count($vats) == 0) {
            $this->errorMessage = 'Configureer eerst de btw percentages alvorens facturen aan te maken';
        }
        
        
        $invoiceForm = $this->oc->create(InvoiceForm::class);
        $invoiceForm->bind($invoice);
        
        // invoice locked?
        if ($invoiceSettings->invoiceLocked( $invoice )) {
            $invoiceForm->setObjectLocked( true );
        }
        
        if (is_post()) {
            $invoiceForm->bind($_REQUEST);
            
            if ($invoiceForm->validate()) {
                if ((get_var('print') || get_var('sendmail')) && $invoiceForm->isObjectLocked()) {
                    // print/sendmail-clicked & object locked? => don't save
                } else {
                    $invoiceService->saveInvoice($invoiceForm);
                }
                
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
        
        if ($invoice->isNew() == false && $invoice->getCreditInvoice() == false) {
            $creditInvoiceId = $invoiceService->lookupCreditInvoiceId( $invoice->getInvoiceId() );
            
            if ($creditInvoiceId) {
                $this->actionContainer->addItem('credit-invoice', '<a href="'.appUrl('/?m=invoice&c=invoice&a=edit&id='.$creditInvoiceId).'">Bekijk creditfactuur</a>');
            } else {
                $this->actionContainer->addItem('credit-invoice', '<a href="'.appUrl('/?m=invoice&c=invoice&a=create_credit&id='.$invoice->getInvoiceId()).'" onclick="'.esc_attr("showConfirmation('Crediteren', 'Weet u zeker dat u deze factuur wilt crediteren?', function() { window.location=$(this).attr('href'); }.bind(this)); return false;").'">Crediteren</a>');
            }
        }
        
        $eb->publishEvent($this->actionContainer, 'invoice', 'invoice-edit');
        
        
        $this->render();
    }
    
    
    public function action_create_credit() {
        
        $invoiceService = $this->oc->get(InvoiceService::class);
        
        $invoice = $invoiceService->readInvoice(get_var('id'));
        if ($invoice == null)
            return $this->renderError('Invoice not found');
        
        $defaultInvoiceStatus = $invoiceService->readDefaultInvoiceStatus();
        
        $newInvoice = new Invoice();
        $newInvoice->setRefInvoiceId( $invoice->getInvoiceId() );
        $newInvoice->setCompanyId( $invoice->getCompanyId() );
        $newInvoice->setPersonId( $invoice->getPersonId() );
        $newInvoice->setInvoiceStatusId( $defaultInvoiceStatus->getInvoiceStatusId() );
        $newInvoice->setCreditInvoice( true );
        $newInvoice->setSubject('Credit: ' . $invoice->getInvoiceNumberText());
        $newInvoice->setComment( $invoice->getComment() );
        $newInvoice->setNote( $invoice->getNote() );
        $newInvoice->setInvoiceDate(date('Y-m-d'));
        
        $newLines = array();
        foreach($invoice->getInvoiceLines() as $il) {
            $nil = new InvoiceLine();
            $nil->setFields( $il->getFields() );
            
            $nil->setPrice( $nil->getPrice() * -1 );
            
            $nil->setInvoiceId(null);
            $nil->setInvoiceLineId(null);
            
            $newLines[] = $nil;
        }
        $newInvoice->setInvoiceLines($newLines);
        
        $form = new InvoiceForm();
        $form->bind( $newInvoice );
        
        $i = $invoiceService->saveInvoice( $form );
        
        redirect('/?m=invoice&c=invoice&a=edit&id=' . $i->getInvoiceId());
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
    
    
    public function action_select2() {
        $invoiceService = $this->oc->get(InvoiceService::class);
        
        $opts = array();
        $opts['invoiceNumberText'] = get_var('name');
        if ((int)get_var('company_id'))
            $opts['company_id'] = (int)get_var('company_id');
        if ((int)get_var('person_id'))
            $opts['person_id'] = (int)get_var('person_id');
        
        $r = $invoiceService->searchInvoice(0, 20, $opts);
        
        $arr = array();
        if (isset($_REQUEST['name']) == false || trim($_REQUEST['name']) == '') {
            $arr[] = array(
                'id' => '0',
                'text' => 'Maak uw keuze'
            );
        }
        foreach($r->getObjects() as $invoice) {
            $arr[] = array(
                'id' => $invoice['invoice_id'],
                'text' => $invoice['invoiceNumberText']
            );
        }
        
        $result = array();
        $result['results'] = $arr;
        
        $this->json($result);
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
            
            include_component('customer', 'company', 'widget', array('company_id' => $companyId));
        }
        
        if (strpos($_REQUEST['customerCode'], 'person-') === 0) {
            $personId = substr($_REQUEST['customerCode'], strlen('person-'));
            
            include_component('customer', 'person', 'widget', array('person_id' => $personId));
        }
        
    }
    
    
    public function action_sendmail() {
        // create PDF
        $invoiceService = $this->oc->get(InvoiceService::class);
        $invoice = $invoiceService->readInvoice((int)$_REQUEST['id']);
        
        $invoicePdf = $invoiceService->createPdf( $invoice->getInvoiceId() );
        
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
        $vars['document_no'] = $invoice->getInvoiceNumberText();
        
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
