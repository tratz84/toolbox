<?php




use core\controller\BaseController;
use mollie\service\MollieService;

class testController extends BaseController {
    
    
    
    
    public function action_index() {
        $mservice = object_container_get( MollieService::class );
        
        $payment = $mservice->createPayment("5.00", 'Test', [
            'metadata' => ['order_id' => 'TEST123'],
            'return_url' => 'http://soupenzo.dev.itxplain.nl/successio'
        ]);
        
        $checkoutUrl = $payment->getCheckoutUrl();
        redirect( $checkoutUrl );
        
    }
    
    
    
}



