<?php

namespace payment\import;


class PaymentSheetImporter {
    
    protected $sheetFile = null;
    
    protected $mapping = null;
    
    
    public function __construct() {
        
    }
    
    
    public function setSheetFile($f) { $this->sheetFile = $f; }
    public function setMapping($m) { $this->mapping = $m; }
    
    
    public function parseRow($rowNo) {
        $m = array();
        
        
        
        
        return $m;
    }
    
}

