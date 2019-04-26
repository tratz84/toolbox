<?php


use core\controller\BaseController;
use invoice\service\PaymentService;

class paymentController extends BaseController {
    
    
    
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