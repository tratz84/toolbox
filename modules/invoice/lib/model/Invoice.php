<?php


namespace invoice\model;


class Invoice extends base\InvoiceBase {

    protected $customer;
    protected $invoiceLines = array();
    
    public function __construct($id=null) {
        parent::__construct($id);

        $this->setInvoiceDate(date('Y-m-d'));
    }

    public function getInvoiceNumberText() {
        $on = $this->getInvoiceNumber();
        
        return \core\Context::getInstance()->getPrefixNumbers() . $on;
    }
    
    
    public function getCustomer() { return $this->customer; }
    public function setCustomer($customer) { $this->customer = $customer; }
    
    public function getInvoiceLines() { return $this->invoiceLines; }
    public function setInvoiceLines($il) { $this->invoiceLines = $il; }
    
    
    public function hasComment() { return trim($this->getComment()) != '' ? true : false; }
    
    
    
    public function getTotalAmountExclVat() {
        $a = 0;
        
        foreach($this->invoiceLines as $il) {
            $a += myround(($il->getAmount() * $il->getPrice()), 2);
        }
        
        return $a;
    }
    
    public function getTotalAmountInclVat() {
        $a = myround($this->getTotalAmountExclVat() + $this->getTotalVat(), 2);
        
        return $a;
    }
    

    public function getTotalVatByPercentage() {
        $l = array();
        
        foreach($this->invoiceLines as $il) {
            $p = $il->getVatPercentage();
            
            if (isset($l[$p]) == false)
                $l[$p]=0;
            
            $l[$p] += $il->getVatAmount();
        }
        
        $returnList = array();
        
        // sort percentages from high to low
        $keys = array_keys($l);
        usort($keys, function($k1, $k2) {
            return intval($k2*100) - intval($k1*100);
        });
        
        foreach($keys as $k) {
            $returnList[$k] = myround($l[$k],2);
        }
        
        return $returnList;
    }
    
    public function getTotalVat() {
        $a = 0;
        
        foreach($this->invoiceLines as $il) {
            $a += $il->getVatAmount();
        }
        
        return myround($a, 2);
    }
    
}

