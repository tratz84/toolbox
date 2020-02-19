<?php



class PaymentImportMatcher {
    
    protected $paymentImportId;
    
    public function __construct($paymentImportId) {
        $this->paymentImportId = $paymentImportId;
        
    }
    
    public function setPaymentImportId($id) { $this->paymentImportId = $id; }
    public function getPaymentImportId($id) { return $this->paymentImportId; }
    
    
    public function match() {
        $updatedLines = array();
        
        
        
    }
    
    
}

