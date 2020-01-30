<?php


use core\controller\BaseController;
use payment\form\PaymentForm;
use payment\service\PaymentService;
use payment\model\Payment;


class paymentController extends BaseController {
    
    
    public function action_index() {
        
        $this->form = new PaymentForm();
        
        $paymentService = object_container_get(PaymentService::class);
        if (get_var('id')) {
            $payment = $paymentService->readPayment( get_var('id') );
        } else {
            $payment = new Payment();
            $payment->setDescription('Handmatig verwerkte betaling');
        }
        
        $this->form->bind($payment);
        
        
        if (is_post()) {
            $this->form->bind( $_REQUEST );
            
            if ($this->form->validate()) {
                $paymentService->savePayment($this->form);
                
                redirect('/?m=payment&c=paymentOverview');
            }
        }
        
        
        $this->isNew = $payment->isNew();
        $this->paymentId = $payment->getPaymentId();
        
        
        if ($this->isNew) {
            checkCapability('payment', 'edit-payments');
        }
        
        
        return $this->render();
    }
    
    
    
    public function action_delete() {
        $paymentService = $this->oc->get(PaymentService::class);
        
        $paymentService->deletePayment($_REQUEST['id']);
        
        if (get_var('back_url')) {
            redirect(get_var('back_url'));
        } else {
            redirect('/?m=payment&c=payment');
        }
    }
    
}