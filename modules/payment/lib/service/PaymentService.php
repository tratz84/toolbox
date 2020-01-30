<?php

namespace payment\service;

use core\service\ServiceBase;
use payment\model\PaymentMethodDAO;
use payment\model\PaymentMethod;
use payment\form\PaymentMethodForm;

class PaymentService extends ServiceBase {
    
    
    
    
    /*
    public function search($start, $limit=20, $opts=array()) {
        $pDao = new PaymentDAO();
        
        $cursor = $pDao->search($opts);
        
        $r = ListResponse::fillByCursor($start, $limit, $cursor, array('payment_id', 'ref_object', 'ref_id', 'payment_method_id', 'person_id', 'company_id', 'invoice_id', 'invoice_line_id', 'description', 'note', 'amount', 'payment_type', 'payment_date', 'created'));
        
        $objects = $r->getObjects();
        foreach($objects as &$o) {
            $o['paymentTypeText'] = t('payment_type.'.$o['payment_type']);
        }
        $r->setObjects($objects);
        
        return $r;
    }
    
    public function readPayment($id) {
        $pDao = new PaymentDAO();
        
        return $pDao->read($id);
    }
    
    public function deletePayment($id, $opts=array()) {
        $p = $this->readPayment($id);
        
        if (!$p) {
            throw new ObjectNotFoundException('Payment object not found');
        }
        
        $p->delete($p->getPaymentId());
        
        // broadcast payment is deleted.
        // ie, if it's an deposit-payment, deposit will be mutated by handler
        $eb = $this->oc->get(EventBus::class);
        $eb->publishEvent($p, 'invoice', 'payment-delete');
        
        // flexible description
        $shortDescription = 'Betaling geannuleerd ('.format_price($p->getAmount()).')';
        if (isset($opts['short_description']) && $opts['short_description'])
            $shortDescription = $opts['short_description'];
        
        ActivityUtil::logActivity($p->getCompanyId(), $p->getPersonId(), $p->getRefObject(), $p->getRefId(), 'payment-cancelled', $shortDescription);
    }
    */
    
    public function readAllMethods() {
        $pmDao = new PaymentMethodDAO();
        
        return $pmDao->readAll();
    }
    
    public function readActiveMethods() {
        $pmDao = new PaymentMethodDAO();
        
        return $pmDao->readActive();
    }
    
    public function readMethodByCode($paymentMethodCode) {
        $pmDao = new PaymentMethodDAO();
        
        return $pmDao->readByCode($paymentMethodCode);
    }
    
    public function readPaymentMethod($paymentMethodId) {
        $pmDao = new PaymentMethodDAO();
        return $pmDao->read($paymentMethodId);
    }
    
    
    public function updatePaymentMethodSort($ids) {
        $pmDao = new PaymentMethodDAO();
        $pmDao->updateSort($ids);
    }
    
    
    public function savePaymentMethod(PaymentMethodForm $form) {
        $id = $form->getWidgetValue('payment_method_id');
        
        if ($id) {
            $paymentMethod = $this->readPaymentMethod($id);
        } else {
            $paymentMethod = new PaymentMethod();
        }
        
        $form->fill($paymentMethod, array('payment_method_id', 'code', 'description', 'default_selected', 'active', 'note'));
        
        if (!$paymentMethod->save()) {
            return false;
        }
        
        if ($paymentMethod->getDefaultSelected()) {
            $pmDao = new PaymentMethodDAO();
            $pmDao->unsetDefaultSelected($paymentMethod->getPaymentMethodId());
        }
        
    }
    
    
    public function deletePaymentMethod($paymentMethodId) {
//         $pDao = new PaymentDAO();
//         $pDao->paymentMethodToNull($paymentMethodId);
        
        $pmDao = new PaymentMethodDAO();
        $pmDao->delete($paymentMethodId);
    }
    
    
    /*
    public function readTotalsForPeriod($start, $end, $refObject=null, $paymentType=null, $paymentMethodId=null) {
        $pDao = new PaymentDAO();
        
        return $pDao->readTotalsForPeriod($start, $end, $refObject, $paymentType, $paymentMethodId);
    }
    */
    
    
    
    public function handleImportFile($file) {
        
        
        
    }
    
    
    
}

