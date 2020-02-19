<?php



use payment\service\PaymentImportService;

class PaymentImportMatcher {
    
    protected $paymentImportId;
    
    public function __construct($paymentImportId) {
        $this->paymentImportId = $paymentImportId;
        
    }
    
    public function setPaymentImportId($id) { $this->paymentImportId = $id; }
    public function getPaymentImportId($id) { return $this->paymentImportId; }
    
    
    public function match() {
        $updatedLines = array();
        
        $piService = object_container_get(PaymentImportService::class);
        $pi = $piService->readImport( $this->paymentImportId );
        
        
        $lines = $pi->getImportLines();
        for($x=0; $x < count($lines); $x++) {
            
        }
        
    }
    
    
}

