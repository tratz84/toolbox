<?php

namespace invoice\service;


use core\exception\ObjectNotFoundException;
use core\service\ServiceBase;
use invoice\model\OfferStatusDAO;
use invoice\model\OrderDAO;
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
    
    
}

