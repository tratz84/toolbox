<?php

namespace invoice\pdf;


class PriceLine {
    
    protected $type = 'price';
    
    protected $description;
    protected $description2;
    protected $amount;
    protected $vatAmount;
    protected $totalPrice;
    protected $price;
    
    
    public function __construct($type='price', $desc='', $amount='', $price='', $desc2=null) {
        
        $this->setType($type);
        $this->setDescription($desc);
        $this->setAmount($amount);
        $this->setPrice($price);
        $this->setDescription2($desc2);
    }
    
    public function getType() { return $this->type; }
    public function setType($t) { $this->type = $t; }
    
    public function getDescription() { return $this->description; }
    public function setDescription($d) { $this->description = $d; }

    public function getDescription2() { return $this->description2; }
    public function setDescription2($d) { $this->description2 = $d; }
    
    public function getAmount() { return $this->amount; }
    public function setAmount($d) { $this->amount = $d; }
    
    public function getVatAmount() { return $this->vatAmount; }
    public function setVatAmount($d) { $this->vatAmount = $d; }
    
    public function getTotalPrice() { return $this->totalPrice; }
    public function setTotalPrice($d) { $this->totalPrice = $d; }
    
    public function getPrice() { return $this->price; }
    public function setPrice($d) { $this->price = $d; }
    
    
}
