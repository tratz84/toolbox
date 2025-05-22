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
use core\exception\InvalidStateException;
use core\forms\lists\ListResponse;



class MollieService extends ServiceBase {
    
    
    
    /**
     *
     * @param array $opts [
     *  'metadata' => ['order_id' => ...],
     *  'return_url' => 'https://...'
     * ]
     */
    public function createPayment( $amount, $description, $opts=array() ) {
        require_once __DIR__.'/../vendor/autoload.php';
        
        $mollie = new MollieApiClient();
        $mollie->setApiKey( ctx()->getSetting( 'mollie_api_key' ) );
        
        $metadata = null;
        
        if (isset($opts['metadata'])) {
            $metadata = $opts['metadata'];
        }
        
        
        $mp = new MolliePayment();
        $mp->setAmount( $amount );
        $mp->setDescription( $description );
        $mp->generateUid();
        if (isset($opts['return_url'])) {
            $mp->setReturnUrl( $opts['return_url'] );
        }
        $mp->setMeta( serialize($metadata) );
        $mp->save();
        
        $puid = $mp->getMolliePaymentId() . '.' . $mp->getUid();
        
        $r = new CreatePaymentRequest(
              $description                                      // description
            , new Money('EUR', $amount)                         // amount
            , BASE_URL . appUrl( '/mollie/return' ).'?uid='.urlencode($puid)                // redirect url
            , BASE_URL . appUrl( '/mollie/return-cancel' ).'?uid='.urlencode($puid)         // cancel url
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

        $mp->setMollieId( $payment->id );
        $mp->setMollieStatus( $payment->status );
        $mp->save();
        
        
        ActivityUtil::logActivityRefObject(MolliePayment::class, $mp->getMolliePaymentId(), 'payment-created', 'New payment created '.$mp->getAmount(), $mp->getAmount() . ' - ' . $mp->getDescription());
        
        return $payment;
    }
    
    
//     public function startPayment( $amount, $description, $opts=null ) {
//         $payment = $this->createPayment($amount, $description, $opts);
//         $checkoutUrl = $payment->getCheckoutUrl();
//         redirect( $checkoutUrl );
//     }
    
    
    
    
    public function checkStatusByUid( $pid_uid ) {
        require_once __DIR__.'/../vendor/autoload.php';
        
        list( $pid, $uid ) = explode('.', $pid_uid, 2);
        
        // fetch mollie_payment-record
        $mpdao = new MolliePaymentDAO();
        $p = $mpdao->read( $pid );
        if (!$p) {
            throw new ObjectNotFoundException( 'Mollie Payment not found' );
        }
        
        if ($p->getUid() != $uid) {
            throw new InvalidStateException( 'Invalid checksum' );
        }
        
        return $this->checkStatusPayment( $p->getMolliePaymentId() );
    }
    
    
    public function checkStatusPayment( $id ) {
        
        $mpdao = new MolliePaymentDAO();
        $p = $mpdao->read($id);
        
        if (!$p) {
            throw new ObjectNotFoundException( 'Mollie Payment not found' );
        }
        
        if ($p->getMollieStatus() == 'open') {
            $mollie = new MollieApiClient();
            $mollie->setApiKey( ctx()->getSetting( 'mollie_api_key' ) );
            
            $pr = new GetPaymentRequest( $p->getMollieId() );
            $payment = $mollie->send( $pr );
            
            if ($p->getMollieStatus() != $payment->status) {
                ActivityUtil::logActivityRefObject( MolliePayment::class, $p->getMolliePaymentId(), 'payment-updatedreated', 'Payment updated', $p->getMollieStatus() . ' => ' . $payment->status );
                
                $p->setMollieStatus( $payment->status );
                $mpdao->updateStatus( $p->getMolliePaymentId(), $payment->status );
            }
        }
        
        return $p;
    }
    
    
    
    public function searchPayments($start, $limit, $opts = array()) {
        
        $mpdao = new MolliePaymentDAO();
        
        $cursor = $mpdao->search($opts);
        $r = ListResponse::fillByCursor($start, $limit, $cursor, array('mollie_payment_id', 'description', 'amount', 'created', 'mollie_status'));
        
        return $r;
    }
    
    
    
}


