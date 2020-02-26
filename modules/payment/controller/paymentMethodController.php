<?php

use core\controller\BaseController;
use core\forms\lists\ListResponse;
use payment\form\PaymentMethodForm;
use payment\service\PaymentService;
use payment\model\PaymentMethod;

class paymentMethodController extends BaseController {
    
    public function init() {
        checkCapability('base', 'edit-masterdata');
        
        $this->addTitle( t('Master data') );
        $this->addTitle( t('Payment methods') );
    }
    
    
    public function action_index() {
        
        $this->render();
    }
    
    public function action_edit() {
        $id = isset($_REQUEST['id'])?(int)$_REQUEST['id']:0;
        
        $paymentService = $this->oc->get(PaymentService::class);
        if ($id) {
            $paymentMethod = $paymentService->readPaymentMethod($id);
            
            $this->addTitle( t('Edit payment method') . ' ' . $paymentMethod->getDescription() );
        } else {
            $paymentMethod = new PaymentMethod();
            
            $this->addTitle( t('New payment method') );
        }
        
        $paymentMethodForm = new PaymentMethodForm();
        $paymentMethodForm->bind($paymentMethod);
        
        if (is_post()) {
            $paymentMethodForm->bind($_REQUEST);
            
            if ($paymentMethodForm->validate()) {
                $paymentService->savePaymentMethod($paymentMethodForm);
                
                redirect('/?m=payment&c=paymentMethod');
            }
            
        }
        
        
        
        $this->isNew = $paymentMethod->isNew();
        $this->form = $paymentMethodForm;
        
        
        $this->render();
        
    }
    
    
    
    public function action_search() {
        $paymentService = $this->oc->get(PaymentService::class);
        
        $paymentMethods = $paymentService->readAllMethods();
        
        $list = array();
        foreach($paymentMethods as $pm) {
            $list[] = $pm->getFields(array('payment_method_id', 'code', 'description', 'active', 'default_selected'));
        }
        
        
        $lr = new ListResponse(0, count($paymentMethods), count($paymentMethods), $list);
        
        $arr = array();
        $arr['listResponse'] = $lr;
        
        $this->json($arr);
    }
    
    public function action_sort() {
        if (isset($_REQUEST['ids'])) {
            $ids = explode(',', $_REQUEST['ids']);
            
            $ps = $this->oc->get(PaymentService::class);
            $ps->updatePaymentMethodSort($ids);
            
        }
        
        print 'OK';
    }
    
    
    public function action_delete() {
        
        $paymentMethodService = $this->oc->get(PaymentService::class);
        $paymentMethodService->deletePaymentMethod($_REQUEST['id']);
        
        redirect('/?m=payment&c=paymentMethod');
    }

}

