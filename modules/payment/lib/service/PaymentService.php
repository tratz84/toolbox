<?php

namespace payment\service;

use base\forms\FormChangesHtml;
use base\util\ActivityUtil;
use core\exception\ObjectNotFoundException;
use core\forms\lists\ListResponse;
use core\service\ServiceBase;
use payment\form\PaymentForm;
use payment\form\PaymentMethodForm;
use payment\model\Payment;
use payment\model\PaymentDAO;
use payment\model\PaymentLineDAO;
use payment\model\PaymentMethod;
use payment\model\PaymentMethodDAO;

class PaymentService extends ServiceBase {

    public function search($start, $limit=20, $opts=array()) {
        $plDao = new PaymentDAO();
        
        $cursor = $plDao->search($opts);
        
        $fields = array();
        $fields[] = 'payment_id';
        $fields[] = 'company_id';
        $fields[] = 'person_id';
        $fields[] = 'payment_description';
        $fields[] = 'payment_note';
        $fields[] = 'payment_amount';
        $fields[] = 'payment_date';
        $fields[] = 'cancelled';
        $fields[] = 'created';
        
        $r = ListResponse::fillByCursor($start, $limit, $cursor, $fields);
        $objects = $r->getObjects();
        
        
        $plDao = new PaymentLineDAO();
        
        $newObjs = array();
        foreach($objects as &$o) {
            $pls = $plDao->readExploded( $o['payment_id'] );
            
            foreach($pls as $pl) {
                $newObjs[] = $pl->getFields();
            }
        }
        $r->setObjects($newObjs);
        
        return $r;
    }
    
    public function searchPaymentLine($start, $limit=20, $opts=array()) {
        $plDao = new PaymentLineDAO();
        
        $cursor = $plDao->search($opts);
        
        $fields = array();
        $fields[] = 'payment_id';
        $fields[] = 'company_id';
        $fields[] = 'person_id';
        $fields[] = 'payment_description';
        $fields[] = 'payment_note';
        $fields[] = 'payment_amount';
        $fields[] = 'payment_date';
        $fields[] = 'cancelled';
        $fields[] = 'created';
        
        $fields[] = 'payment_line_id';
        $fields[] = 'payment_method_id';
        $fields[] = 'payment_line_amount';
        $fields[] = 'bankaccountno';
        $fields[] = 'bankaccountno_contra';
        $fields[] = 'payment_line_code';
        $fields[] = 'payment_line_name';
        $fields[] = 'payment_line_description1';
        $fields[] = 'payment_line_description2';
        $fields[] = 'payment_line_mutation_type';
        $fields[] = 'payment_line_sort';
        
        $fields[] = 'payment_method_code';
        $fields[] = 'payment_method_description';
        $fields[] = 'payment_method_active';
        $fields[] = 'payment_method_deleted';
        
        $fields[] = 'company_name';
        
        $fields[] = 'firstname';
        $fields[] = 'insert_lastname';
        $fields[] = 'lastname';
        
        $r = ListResponse::fillByCursor($start, $limit, $cursor, $fields);
        
        $objects = $r->getObjects();
        foreach($objects as &$o) {
//             $o['paymentTypeText'] = t('payment_type.'.$o['payment_type']);
        }
        $r->setObjects($objects);
        
        return $r;
    }
    
    /*
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
    
    public function readDefaultSelectedPaymentMethod() {
        $pmDao = new PaymentMethodDAO();
        $p = $pmDao->readDefaultSelected();
        
        if ($p == null) {
            $pms = $pmDao->readActive();
            if (count($pms) > 0) {
                $p = $pms[0];
            }
        }
        
        return $p;
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
    
    
    public function readPayment($paymentId) {
        $pDao = new PaymentDAO();
        $payment = $pDao->read( $paymentId );
        
        if (!$payment) {
            throw new ObjectNotFoundException('Payment not found');
        }
        
        $plDao = new PaymentLineDAO();
        $lines = $plDao->readByPayment($payment->getPaymentId());
        
        $payment->setPaymentLines($lines);
        
        return $payment;
    }
    
    public function deletePayment($paymentId) {
        $payment = $this->readPayment($paymentId);
        
        if (!$payment) {
            throw new ObjectNotFoundException('Payment not found');
        }
        
        $plDao = new PaymentLineDAO();
        $plDao->deleteByPayment($payment->getPaymentId());
        
        $pDao = new PaymentDAO();
        $pDao->delete($payment->getPaymentId());
        
        $form = new PaymentForm();
        $form->bind($payment);
        $fch = FormChangesHtml::formDeleted($form);
        
        
        ActivityUtil::logActivity($payment->getCompanyId(), $payment->getPersonId(), 'payment__payment', $payment->getPaymentId(), 'payment-deleted', 'Betaling verwijderd '.$payment->getPaymentId(), $fch->getHtml());
    }
    
    
    public function savePayment(PaymentForm $form) {
        $paymentId = $form->getWidgetValue('payment_id');
        if ($paymentId) {
            $payment = object_container_get(PaymentService::class)->readPayment($paymentId);
        } else {
            $payment = new Payment();
        }
        
        $isNew = $payment->isNew();
        
        if ($isNew) {
            $fch = FormChangesHtml::formNew($form);
        } else {
            $oldForm = PaymentForm::createAndBind($payment);
            $fch = FormChangesHtml::formChanged($oldForm, $form);
        }
        
        
        if ($fch->hasChanges() == false) {
            return $payment;
        }
        
        
        $form->fill($payment, array('payment_id', 'payment_date', 'cancelled', 'description', 'note'));
        
        $payment->setCompanyId(null);
        $payment->setPersonId(null);
        
        // set customer
        $customer_id = $form->getWidgetValue('customer_id');
        $company_id = $person_id = null;
        if (strpos($customer_id, 'company-') === 0) {
            $company_id = substr($customer_id, strlen('company-'));
            $payment->setCompanyId($company_id);
        } else if (strpos($customer_id, 'person-') === 0) {
            $person_id = substr($customer_id, strlen('person-'));
            $payment->setPersonId($person_id);
        }
        
        $newPls = $form->getWidget('PaymentLines')->asArray();
        
        // set total amount
        $a = 0;
        foreach($newPls as $pl) {
            $a += strtodouble( $pl['amount'] );
            $payment->setAmount( $a );
        }
        
        
        
        if (!$payment->save()) {
            // exception would also be on it's place
            return false;
        }
        
        $form->getWidget('payment_id')->setValue($payment->getPaymentId());
        
        $plDao = new PaymentLineDAO();
//         var_export($newPls);
        $plDao->mergeFormListMTO1('payment_id', $payment->getPaymentId(), $newPls);
        
        if ($isNew) {
            ActivityUtil::logActivity($company_id, $person_id, 'payment__payment', null, 'payment-created', 'Betaling aangemaakt', $fch->getHtml());
        } else {
            // TODO: check of er wijzigingen zijn
            ActivityUtil::logActivity($company_id, $person_id, 'payment__payment', null, 'payment-edited', 'Betaling aangepast', $fch->getHtml());
        }
        
        return $payment;
    }
    
    
    
    
    
    public function handleImportFile($file) {
        
        
        
    }
    
    
    
}

