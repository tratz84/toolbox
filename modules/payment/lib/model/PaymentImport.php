<?php


namespace payment\model;


class PaymentImport extends base\PaymentImportBase {

    protected $importLines = array();
    
    
    public function setImportLines($lines) { $this->importLines = $lines; }
    public function getImportLines() { return $this->importLines; }
    
    public function addImportLine($pil) { $this->importLines[] = $pil; }

}

