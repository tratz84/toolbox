<?php


use base\service\MetaService;
use core\container\ActionContainer;
use core\controller\BaseController;
use core\event\EventBus;
use core\exception\InvalidStateException;
use invoice\InvoiceSettings;
use invoice\form\OrderForm;
use invoice\model\Invoice;
use invoice\model\Order;
use invoice\service\InvoiceService;
use invoice\service\OrderService;
use webmail\model\EmailTo;
use webmail\service\EmailService;
use webmail\service\EmailTemplateService;

class orderController extends BaseController {
    
    public function init() {
        $this->addTitle(t('Orders'));
    }
    
    public function action_index() {
        
        $this->invoiceSettings = $this->oc->get(InvoiceSettings::class);
        
        $orderService = object_container_get(OrderService::class);
        
        $this->orderStatus = array();
        $this->orderStatus[] = array('value' => '', 'text' => 'Status');
        
        $orderStatus = $orderService->readActiveOrderStatus();
        foreach($orderStatus as $os) {
            $this->orderStatus[] = array(
                'value' => $os->getOrderStatusId(),
                'text' => $os->getDescription()
            );
        }
        
        $this->render();
    }
    
    
    public function action_edit() {
        $id = isset($_REQUEST['id'])?(int)$_REQUEST['id']:0;
        
        $orderService = object_container_get(OrderService::class);
        if ($id) {
            $order = $orderService->readOrder($id);
            if ($order == null)
                return $this->renderError('Order not found');
                
                $strTitle = t('Order').' ' . $order->getOrderNumberText();
                if ($order->getCustomer()) {
                    $strTitle .= ' - ' . $order->getCustomer()->getName();
                }
                
                $this->addTitle( $strTitle );
        } else {
            $this->addTitle(t('New order'));
            
            $order = new Order();
        }
        
        // there must be atleast 1 active vat-tarif
        $invoiceService = object_container_get(InvoiceService::class);
        $vats = $invoiceService->readActiveVatTarifs();
        if (count($vats) == 0) {
            $this->errorMessage = 'Configureer eerst de btw percentages alvorens orders aan te maken';
        }
        
        
        $orderForm = new OrderForm();
        $orderForm->bind($order);
        
        if (is_post()) {
            // locked & print? => skip saving
            if (dbobject_is_locked($order) && get_var('print')) {
                $url = '/?m=invoice&c=order&a=print&id=' . $orderForm->getWidgetValue('order_id');
                redirect($url);
            }
            // locked & generate invoice?
            else if (dbobject_is_locked($order) && get_var('generateInvoice')) {
                redirect('/?m=invoice&c=order&a=generate_invoice&id=' . $orderForm->getWidgetValue('order_id'));
            }
            // locked & sendmail?
            else if (dbobject_is_locked($order) && get_var('sendmail')) {
                redirect('/?m=invoice&c=order&a=sendmail&id=' . $orderForm->getWidgetValue('order_id'));
            }
            
            check_dbobject_locked($order);
            
            $orderForm->bind($_REQUEST);
            
            if ($orderForm->validate()) {
                $orderService->saveOrder($orderForm);
                
                if (get_var('print')) {
                    $url = '/?m=invoice&c=order&a=print&id=' . $orderForm->getWidgetValue('order_id');
                } else if (get_var('generateInvoice')) {
                    $url = '/?m=invoice&c=order&a=generate_invoice&id=' . $orderForm->getWidgetValue('order_id');
                } else if (get_var('sendmail')) {
                    $url = '/?m=invoice&c=order&a=sendmail&id=' . $orderForm->getWidgetValue('order_id');
                } else {
                    report_user_message('Wijzigingen opgeslagen');
                    
                    $url = '/?m=invoice&c=order&a=edit&id=' . $orderForm->getWidgetValue('order_id');
                }
                
                redirect($url);
            }
            
        }
        
        
        $this->invoiceId = null;
        if ($order->isNew() == false) {
            $metaService = $this->oc->get(MetaService::class);
            $this->invoiceId = $metaService->getIdByObjectValue(Invoice::class, 'order_id', $order->getOrderId());
        }
        
        $this->isNew = $order->isNew();
        $this->order = $order;
        $this->form = $orderForm;
        
        
        $eb = $this->oc->get(EventBus::class);
        $this->actionContainer = new ActionContainer('order', $order->getOrderId());
        
        $this->actionContainer->addItem('create-invoice', '<a href="javascript:void(0);" onclick="generateInvoice();">'.strOrder(1).' aanmaken</a>', 5);
        
        $eb->publishEvent($this->actionContainer, 'invoice', 'order-edit');
        
        
        $this->render();
    }
    
    
    
    public function action_update_status() {
        $orderService = object_container_get(OrderService::class);
        
        $orderService->updateOrderStatus($_REQUEST['order_id'], $_REQUEST['order_status_id']);
        
        $this->json(array(
            'status' => 'OK'
        ));
    }
    
    
    public function action_popup_status() {
        $orderService = object_container_get(OrderService::class);
        
        $this->orderStatus = $orderService->readActiveOrderStatus();
        
        $this->setShowDecorator(false);
        $this->render();
    }
    
    
    public function action_print() {
        $orderService = object_container_get(OrderService::class);
        $order = $orderService->readOrder((int)$_REQUEST['id']);
        $orderPdf = $orderService->createPdf((int)$_REQUEST['id']);
        
        @$orderPdf->Output('I', 'order-'.$order->getOrderNumberText().'.pdf', true);
    }
    
    
    
    public function action_search() {
        $pageNo = isset($_REQUEST['pageNo']) ? (int)$_REQUEST['pageNo'] : 0;
        $limit = $this->ctx->getPageSize();
        
        $orderService = object_container_get(OrderService::class);
        
        $r = $orderService->searchOrder($pageNo*$limit, $limit, $_REQUEST);
        
        $arr = array();
        $arr['listResponse'] = $r;
        
        
        $this->json($arr);
    }
    
    public function action_delete() {
        $orderService = object_container_get(OrderService::class);
        
        $orderService->deleteOrder($_REQUEST['id']);
        
        redirect('/?m=invoice&c=order');
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
        $orderService = object_container_get(OrderService::class);
        $order = $orderService->readOrder((int)$_REQUEST['id']);
        
        $orderPdf = $orderService->createPdf((int)$_REQUEST['id']);
        
        $rawPdfData = $orderPdf->Output('S');
        
        // build template
        $emailTemplateService = $this->oc->get(EmailTemplateService::class);
        $template = $emailTemplateService->readByTemplateCode('ORDER_MAIL');
        
        if ($template == null) {
            throw new InvalidStateException('ORDER_MAIL-template not found');
        }
        
        $vars = array();
        $vars = $order->getCustomer()->getTemplateVars();
        
        $vars['betreft'] = $order->getSubject();
        $vars['document_no'] = $order->getOrderNumberText();
        $vars = array_merge($order->getFields(), $vars);
        
        $html = $template->render($vars);
        $files = array();
        $files[] = array(
            'filename' => 'order-'.$order->getOrderNumberText().'.pdf',
            'data' => $rawPdfData
        );
        
        // add uploaded attachments
        $orderAttachments = list_data_files('attachments/order/');
        foreach($orderAttachments as $oaFilename) {
            $path = get_data_file('attachments/order/'.$oaFilename);
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
        $e->setCompanyId($order->getCompanyId());
        $e->setPersonId($order->getPersonId());
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
        
        
        $emailAddresses = $order->getCustomer()->getEmailList();
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
        
        $orderService = object_container_get(OrderService::class);
        $invoice = $orderService->createInvoice((int)get_var('id'));
        
        redirect('/?m=invoice&c=invoice&a=edit&id=' . $invoice->getInvoiceId());
    }
    
    
}


