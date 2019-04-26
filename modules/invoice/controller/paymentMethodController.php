<?php


use core\controller\BaseController;
use core\forms\lists\ListResponse;
use invoice\form\PaymentMethodForm;
use invoice\model\PaymentMethod;
use invoice\service\PaymentService;

class paymentMethodController extends BaseController {
    
    public function init() {
        checkCapability('base', 'edit-masterdata');
    }
    
    
    public function action_index() {
        
        $this->render();
    }
    
    public function action_edit() {
        $id = isset($_REQUEST['id'])?(int)$_REQUEST['id']:0;
        
        $paymentService = $this->oc->get(PaymentService::class);
        if ($id) {
            $paymentMethod = $paymentService->readPaymentMethod($id);
        } else {
            $paymentMethod = new PaymentMethod();
        }
        
        $paymentMethodForm = new PaymentMethodForm();
        $paymentMethodForm->bind($paymentMethod);
        
        if (is_post()) {
            $paymentMethodForm->bind($_REQUEST);
            
            if ($paymentMethodForm->validate()) {
                $paymentService->savePaymentMethod($paymentMethodForm);
                
                redirect('/?m=invoice&c=paymentMethod');
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
        
        redirect('/?m=invoice&c=paymentMethod');
    }
    
    
}


