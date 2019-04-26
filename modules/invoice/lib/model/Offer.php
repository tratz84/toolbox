<?php


namespace invoice\model;


class Offer extends base\OfferBase {

    protected $customer;
    protected $offerLines;
    
    public function __construct($id=null) {
        parent::__construct($id);
        
        $this->setAccepted(0);
        $this->setOfferDate(date('Y-m-d'));
        
    }
    
    
    public function getOfferNumberText() {
        $on = $this->getOfferNumber();
        
        return \core\Context::getInstance()->getPrefixNumbers() . $on;
    }
    
    
    public function getCustomer() { return $this->customer; }
    public function setCustomer($customer) { $this->customer = $customer; }
    
    public function getOfferLines() { return $this->offerLines; }
    public function setOfferLines($lines) { $this->offerLines = $lines; }

    public function getTotalAmountExclVat() {
        $a = 0;
        
        foreach($this->offerLines as $ol) {
            $a += myround($ol->getAmount() * $ol->getPrice(), 2);
        }
        
        return $a;
    }
    
    public function getTotalAmountInclVat() {
        $a = myround($this->getTotalAmountExclVat() + $this->getTotalVat(), 2);
        
        return $a;
    }
    
    
    
    public function getTotalVatByPercentage() {
        $l = array();
        
        foreach($this->offerLines as $ol) {
            $p = $ol->getVat();
            
            if (!$p) continue;
            
            if (isset($l[$p]) == false)
                $l[$p]=0;
            
            $l[$p] += $ol->getVatAmount();
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
        
        foreach($this->offerLines as $ol) {
            $a += myround($ol->getVatAmount(), 2);
        }
        
        return $a;
    }
    
    
    public function save() {
        
        if ($this->isNew()) {
            $oDao = new OfferDAO();
            $no = $oDao->generateOfferNumber();
            
            $this->setOfferNumber( $no );
        }
        
        return parent::save();
    }
    
    
    
}

