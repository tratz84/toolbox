<?php


namespace invoice\model;


class OfferLine extends base\OfferLineBase {


    public function __construct($id=null) {
        parent::__construct($id);
        
        $this->setLineType('price');
    }
    
    
    public function getVatAmount() {
        $a = $this->getPrice() * $this->getAmount();
        
        $v = ($a * $this->getVat());
        
        return myround($v/100, 2);
    }
    
    public function getTotalPriceInclVat() {
        $a = $this->getPrice() * $this->getAmount();
        $a += myround($this->getVatAmount(),2);
        return $a;
    }
    
    
}

