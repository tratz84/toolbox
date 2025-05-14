<?php


namespace mollie\service;

use core\service\ServiceBase;
use Mollie\Api\MollieApiClient;
use Mollie\Api\Http\Requests\CreatePaymentRequest;
use Mollie\Api\Http\Data\Money;
use mollie\model\MolliePayment;
use mollie\model\MolliePaymentDAO;
use core\exception\ObjectNotFoundException;
use Mollie\Api\Http\Requests\GetPaymentRequest;
use base\util\ActivityUtil;



class MollieService extends ServiceBase {
    
    
    
    
    public function createPayment( $amount, $description, array $metadata=null ) {
        require_once __DIR__.'/../vendor/autoload.php';
        
        $mollie = new MollieApiClient();
        $mollie->setApiKey( ctx()->getSetting( 'mollie_api_key' ) );
        
        
        $r = new CreatePaymentRequest(
              $description                                      // description
            , new Money('EUR', $amount)                         // amount
            , BASE_URL . appUrl( '/mollie/return' )             // redirect url
            , BASE_URL . appUrl( '/mollie/return-cancel' )      // cancel url
            , BASE_URL . appUrl( '/mollie/webhook' )               // webhook url
            , null                                              // lines
            , null                                              // billing address
            , null                                              // shipping address
            , null                                              // locale
            , null                                              // method
            , null                                              // issuer
            , null                                              // restrictedPaymentMethodsToCountry
            , $metadata
            );

        
        $payment = $mollie->send($r);
        
        $mp = new MolliePayment();
        $mp->setAmount( $amount );
        $mp->setDescription( $description );
        $mp->setMolliePaymentId( $payment->id );
        $mp->setMollieStatus( $payment->status );
        $mp->setMeta( serialize($metadata) );
        $mp->save();
        
        
        ActivityUtil::logActivityRefObject(MolliePayment::class, $mp->getMolliePaymentId(), 'payment-created', 'New payment created '.$mp->getAmount(), $mp->getAmount() . ' - ' . $mp->getDescription());
        
        return $payment;
    }
    
    public function startPayment( $amount, $description, array $metadata=null ) {
        
        $payment = $this->createPayment($amount, $description, $metadata);
        
        $checkoutUrl = $payment->getCheckoutUrl();
        
        redirect( $checkoutUrl );
    }
    
    
    
    
    public function checkStatus( $molliePaymentId ) {
        require_once __DIR__.'/../vendor/autoload.php';
        
        // fetch mollie_payment-record
        $mpdao = new MolliePaymentDAO();
        $p = $mpdao->readByMolliePaymentId( $molliePaymentId );
        if (!$p) {
            throw new ObjectNotFoundException( 'Mollie Payment not found' );
        }
        
        
        $mollie = new MollieApiClient();
        $mollie->setApiKey( ctx()->getSetting( 'mollie_api_key' ) );
        
        $pr = new GetPaymentRequest( $p->getMolliePaymentId() );
        $payment = $mollie->send( $pr );
        
        if ($p->getMollieStatus() != $payment->status) {
            ActivityUtil::logActivityRefObject( MolliePayment::class, $p->getMolliePaymentId(), 'payment-updatedreated', 'Payment updated', $p->getMollieStatus() . ' => ' . $payment->status );
            
            $p->setMollieStatus( $payment->status );
            $mpdao->updateStatus( $p->getMolliePaymentId(), $payment->status );
        }
        
        return $p;
    }
    
    
}


