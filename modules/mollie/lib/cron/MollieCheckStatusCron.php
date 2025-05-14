<?php


namespace mollie\cron;


use core\cron\CronJobBase;
use mollie\model\MolliePaymentDAO;
use mollie\service\MollieService;

class MollieCheckStatusCron extends CronJobBase {
    
    protected $msg = '';
    protected $status = '';
    
    
    public function __construct() {
        $this->title = 'Mollie payments';
        $this->daily = false;
    }
    
    public function getMessage() { return $this->msg; }
    public function getStatus() { return $this->status; }
    
    
    public function checkJob() {
        return true;
    }
    
    
    
    public function run() {
        
        $mpdao = new MolliePaymentDAO();
        $mps = $mpdao->readOpenPayments();
        
        $mservice = object_container_get( MollieService::class );
        foreach($mps as $mp) {
            $mservice->checkStatusPayment( $mp->getMolliePaymentId() );
        }
        
    }
    
    
}

