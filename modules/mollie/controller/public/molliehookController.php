<?php


use core\controller\BaseController;
use mollie\service\MollieService;

class molliehookController extends BaseController {
    
    
    
    public function action_return() {
        $mollie_payment_id = get_var('id');
        
        $mservice = object_container_get( MollieService::class );
        $mservice->checkStatus( $mollie_payment_id );
        
        $this->render();
    }
    
    public function action_return_cancel() {
        $mollie_payment_id = get_var('id');
        
        $mservice = object_container_get( MollieService::class );
        $mservice->checkStatus( $mollie_payment_id );
        
        $this->render();
    }
    
    
    public function action_webhook() {
        $mollie_payment_id = get_var('id');
        
        $mservice = object_container_get( MollieService::class );
        $mservice->checkStatus( $mollie_payment_id );
        
        print 'OK';
    }
    
    
}


