<?php


use core\controller\BaseController;
use payment\form\PaymentForm;

class paymentController extends BaseController {
    
    
    public function action_index() {
        
        
        $this->form = new PaymentForm();
        
        
        
        
        $this->isNew = true;
        
        
        
        if ($this->isNew)
            checkCapability('payment', 'edit-payments');
        
        
        return $this->render();
    }
    
    
    
    public function action_delete() {
        
        
        $paymentService = $this->oc->get(PaymentService::class);
        
        $paymentService->deletePayment($_REQUEST['id']);
        
        if (get_var('back_url')) {
            redirect(get_var('back_url'));
        } else {
            redirect('/');
        }
    }
    
}