<?php


namespace invoice\model;


class PriceAdjustment extends base\PriceAdjustmentBase {

    public function __construct($id=null) {
        parent::__construct($id);
        
        $this->setExecuted(false);
        
    }
    
    
    public function getPriceEuro() {
        return $this->getNewPrice();
    }
    
    
    public function getStartDateFormat($f='d-m-Y') {
        return format_date($this->getStartDate(), $f);
    }

    public function getCreatedFormat($f='d-m-Y') {
        return format_date($this->getCreated(), $f);
    }
    
}

