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
use invoice\form\OrderForm;
use invoice\model\Invoice;
use invoice\model\InvoiceLine;
use invoice\model\Order;
use invoice\model\OrderDAO;
use invoice\model\OrderLine;
use invoice\model\OrderLineDAO;
use invoice\model\OrderStatus;
use invoice\model\OrderStatusDAO;

class OrderService extends ServiceBase {
    
    public function __construct() {
        parent::__construct();
        
    }
    
    
    public function readOrderStatus($id) {
        
        $oDao = new OrderStatusDAO();
        
        $os = $oDao->read($id);
        
        if (!$os) {
            throw new ObjectNotFoundException("OrderStatus not found");
        }
        
        
        return $os;
    }
    
    public function readAllOrderStatus() {
        $oDao = new OrderStatusDAO();
        return $oDao->readAll();
    }
    
    public function readActiveOrderStatus() {
        $oDao = new OrderStatusDAO();
        return $oDao->readActive();
    }
    
    
    public function updateOrderStatusSort($orderStatusIds) {
        $osDao = new OrderStatusDAO();
        $osDao->updateSort($orderStatusIds);
    }
    
    public function saveOrderStatus($form) {
        $id = $form->getWidgetValue('order_status_id');
        if ($id) {
            $orderStatus = $this->readOrderStatus($id);
        } else {
            $orderStatus = new OrderStatus();
        }
        
        $form->fill($orderStatus, array('order_status_id', 'customer_id', 'description', 'default_selected', 'active'));
        
        if (!$orderStatus->save()) {
            return false;
        }
        
        if ($orderStatus->getDefaultSelected()) {
            $oDao = new OrderStatusDAO();
            $oDao->unsetDefaultSelected($orderStatus->getOrderStatusId());
        }
        
        return $orderStatus;
    }
    
    public function readDefaultOrderStatus() {
        $osDao = new OrderStatusDAO();
        
        $os = $osDao->readByDefaultStatus();
        if ($os)
            return $os;
            
            $os = $osDao->readFirst();
            return $os;
    }
    
    
    public function deleteOrderStatus($id) {
        // set order status to null of currently used cases
        $oDao = new OrderDAO();
        $oDao->orderStatusToNull($id);
        
        $osDao = new OrderStatusDAO();
        $osDao->delete($id);
    }
    
    
    
    public function searchOrder($start, $limit, $opts = array()) {
        $oDao = new OrderDAO();
        
        $cursor = $oDao->search($opts);
        
        $r = ListResponse::fillByCursor($start, $limit, $cursor, array('order_id', 'order_status_id', 'company_id', 'person_id', 'total_calculated_price', 'total_calculated_price_incl_vat', 'subject', 'comment', 'accepted', 'order_date', 'edited', 'created', 'firstname', 'insert_lastname', 'lastname', 'company_name', 'order_status_description', 'orderNumberText'));
        
        return $r;
        
    }
    
    public function readOrder($id) {
        $oDao = new OrderDAO();
        
        $order = $oDao->read($id);
        
        if (!$order)
            return null;
        
        $olDao = new OrderLineDAO();
        $lines = $olDao->readByOrder($id);
        $order->setOrderLines( $lines );
        
        $customerService = ObjectContainer::getInstance()->get(CustomerService::class);
        $customer = $customerService->readCustomerAuto($order->getCompanyId(), $order->getPersonId());
        $order->setCustomer($customer);         // might be null
        
        return $order;
    }
    
    
    
    
    
    
    
    
    
    public function saveOrder($form) {
        $id = $form->getWidgetValue('order_id');
        if ($id) {
            $order = $this->readOrder($id);
        } else {
            $order = new Order();
        }
        
        $isNew = $order->isNew();
        
        if ($isNew) {
            $fch = FormChangesHtml::formNew($form);
        } else {
            $oldForm = new OrderForm();
            $oldForm->bind($order);
            
            $fch = FormChangesHtml::formChanged($oldForm, $form);
        }
        
        // no changes? => skip (saveOrder is called when printing or sending email)
        if ($fch->hasChanges() == false) {
            return $order;
        }
        
        $form->fill($order, array('order_id', 'customer_id', 'order_status_id', 'order_date', 'deposit', 'payment_upfront', 'subject', 'comment', 'note'));
        
        if ($order->getOrderStatusId() == 0)
            $order->setOrderStatusId(null);
        
        
        $totalCalculatedAmount = 0;
        $totalCalculatedAmountInclVat = 0;
        $newOrderLines = $form->getWidget('orderLines')->getObjects();
        for($x=0; $x < count($newOrderLines); $x++) {
            if (isset($newOrderLines[$x]['price'])) {
                $price = strtodouble( $newOrderLines[$x]['price'] );
                $vatAmount = myround( $price * strtodouble($newOrderLines[$x]['amount']) * $newOrderLines[$x]['vat_percentage'] / 100, 2 );
                
                $totalCalculatedAmount += myround( $price * $newOrderLines[$x]['amount'], 2 );
                $totalCalculatedAmountInclVat += myround( $price * $newOrderLines[$x]['amount'], 2 ) + $vatAmount;
                
                $newOrderLines[$x]['price'] = $price;
                $newOrderLines[$x]['vat_amount'] = $vatAmount;
            }
        }
        
        $order->setTotalCalculatedPrice( myround($totalCalculatedAmount, 2) );
        $order->setTotalCalculatedPriceInclVat( $totalCalculatedAmountInclVat );
        
        
        if (!$order->save()) {
            return false;
        }
        
        $form->getWidget('order_id')->setValue($order->getOrderId());
        
        $olDao = new OrderLineDAO();
//             $newOrderLines = $form->getWidget('orderLines')->getObjects();
        $olDao->mergeFormListMTO1('order_id', $order->getOrderId(), $newOrderLines);
        
        
        if ($isNew) {
            ActivityUtil::logActivity($order->getCompanyId(), $order->getPersonId(), 'invoice__order', $order->getOrderId(), 'order-created', 'Order aangemaakt '.$order->getOrderNumberText(), $fch->getHtml());
        } else {
            ActivityUtil::logActivity($order->getCompanyId(), $order->getPersonId(), 'invoice__order', $order->getOrderId(), 'order-edited', 'Order aangepast '.$order->getOrderNumberText(), $fch->getHtml());
        }
        
        return $order;
    }
    
    
    
    public function updateOrderStatus($orderId, $orderStatusId) {
        $osNew = $this->readOrderStatus($orderStatusId);
        if ($osNew == null)
            throw new ObjectNotFoundException('OrderStatus not found');
        
        $order = $this->readOrder($orderId);
        if ($order == null)
            throw new ObjectNotFoundException('Order not found');
        
        $osOld = $this->readOrderStatus($order->getOrderStatusId());
        
        
        $oDao = new OrderDAO();
        $oDao->updateStatus($orderId, $orderStatusId);
        
        $html = FormChangesHtml::tableFromArray([
            ['label' => 'Status', 'old' => $osOld?$osOld->getDescription():'', 'new' => $osNew->getDescription()]
        ]);
        
        
        ActivityUtil::logActivity($order->getCompanyId(), $order->getPersonId(), 'invoice__order', $order->getOrderId(), 'order-update-status', 'Order status '.$order->getOrderNumberText() . ': ' . $osNew->getDescription(), $html);
    }
    
    public function deleteOrder($orderId) {
        $orderId = (int)$orderId;
        
        $order = $this->readOrder($orderId);
        
        // track changes
        $orderForm = new OrderForm();
        $orderForm->bind( $order );
        $fch = FormChangesHtml::formDeleted($orderForm);
        
        // delete lines & order
        $olDao = new OrderLineDAO();
        $olDao->deleteByOrder($orderId);
        
        $oDao = new OrderDAO();
        $oDao->delete($orderId);
        
        // log activity
        ActivityUtil::logActivity($order->getCompanyId(), $order->getPersonId(), 'invoice__order', $order->getOrderId(), 'order-deleted', 'Order verwijderd '.$order->getOrderNumberText(), $fch->getHtml());
    }
    
    
    
    public function createPdf($orderId) {
        $order = $this->readorder($orderId);
        
        $invoiceSettings = $this->oc->get(InvoiceSettings::class);
        
        //         $orderPdf = $this->oc->create( \context\ptw\pdf\LandscapeOrderPdf::class );
        $orderPdf = @$this->oc->create( $invoiceSettings->getOrderPdfClass() );
        
        
        $orderPdf->setOrder($order);
        $orderPdf->render();
        
        return $orderPdf;
    }
    
    public function createInvoice($orderId) {
        $order = $this->readOrder($orderId);
        
        $i = new Invoice();
        $i->setPersonId($order->getPersonId());
        $i->setCompanyId($order->getCompanyId());
        $i->setSubject($order->getSubject());
        $i->setComment($order->getComment());
        $i->setInvoiceDate(date('Y-m-d'));
        
        $defaultInvoiceStatus = $this->readDefaultInvoiceStatus();
        if ($defaultInvoiceStatus) {
            $i->setInvoiceStatusId( $defaultInvoiceStatus->getInvoiceStatusId() );
        }
        
        
        $ols = $order->getOrderLines();
        $invoiceLines = array();
        for($x=0; $x < count($ols); $x++) {
            /**
             * @var OrderLine $ol
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
        $metaService->saveMeta(Invoice::class, $i->getInvoiceId(), 'order_id', $order->getOrderId());
        
        return $i;
    }
    
    
    
}

