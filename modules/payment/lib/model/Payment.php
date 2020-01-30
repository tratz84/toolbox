<?php


namespace payment\model;


class Payment extends base\PaymentBase {

    protected $paymentLines = array();
    
    
    public function __construct($id=null) {
        parent::__construct( $id );
        
        $this->setPaymentDate( date('Y-m-d') );
        $this->setCancelled(false);
    }

    public function getPaymentNumberText() {
        $pid = $this->getPaymentId();
        
        return \core\Context::getInstance()->getPrefixNumbers() . $pid;
    }
    
    
    public function getPaymentLines() { return $this->paymentLines; }
    public function setPaymentLines($lines) { $this->paymentLines = $lines; }
    
    
    public function hasNote() {
        $t = trim($this->getNote());
        
        return $t != '' ? true : false;
    }

}

