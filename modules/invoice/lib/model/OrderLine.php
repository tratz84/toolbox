<?php


namespace invoice\model;


class OrderLine extends base\OrderLineBase {

	public function __construct($id=null) {
		parent::__construct( $id );
		
	}
	
	
	
	
	public function getVatAmount() {
	    $a = $this->getPrice() * $this->getAmount();
	    
	    $v = ($a * $this->getVatPercentage());
	    
	    return myround($v/100, 2);
	}
	
	public function getTotalPriceInclVat() {
	    $a = $this->getPrice() * $this->getAmount();
	    $a += myround($this->getVatAmount(),2);
	    return $a;
	}
	
	
}

