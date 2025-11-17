<?php


namespace invoice\model;


class Order extends base\OrderBase {
    
    
    protected $customer;
    protected $orderLines;
    
	public function __construct($id=null) {
		parent::__construct( $id );
		
	}
	
	
	
	
	public function getOrderNumberText() {
	    $on = $this->getOrderNumber();
	    
	    return \core\Context::getInstance()->getPrefixNumbers() . $on;
	}
	
	
	public function getCustomer() { return $this->customer; }
	public function setCustomer($customer) { $this->customer = $customer; }
	
	public function getOrderLines() { return $this->orderLines; }
	public function setOrderLines($lines) { $this->orderLines = $lines; }
	
	public function getTotalAmountExclVat() {
	    $a = 0;
	    
	    foreach($this->orderLines as $ol) {
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
	    
	    foreach($this->orderLines as $ol) {
// 	        $p = $ol->getVat();
	        
// 	        if (!$p) continue;
	        
// 	        if (isset($l[$p]) == false)
// 	            $l[$p]=0;
	            
            $l[$ol->getVatPercentage()] += $ol->getVatAmount();
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
	    
	    foreach($this->orderLines as $ol) {
	        $a += myround($ol->getVatAmount(), 2);
	    }
	    
	    return $a;
	}
	
	
	public function save() {
	    
	    if ($this->isNew()) {
	        $oDao = new OrderDAO();
	        $no = $oDao->generateOrderNumber();
	        
	        $this->setOrderNumber( $no );
	    }
	    
	    return parent::save();
	}
	
}

