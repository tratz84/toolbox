<?php


namespace payment\model;


class Payment extends base\PaymentBase {

    protected $paymentLines = array();
    
    
    public function __construct($id=null) {
        parent::__construct( $id );
        
        $this->setPaymentDate( date('Y-m-d') );
        
    }
    
    
    public function getPaymentLines() { return $this->paymentLines; }
    public function setPaymentLines($lines) { $this->paymentLines = $lines; }
    

}

