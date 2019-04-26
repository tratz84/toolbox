<?php


namespace base\model;


class Address extends base\AddressBase {

    
    public function __construct($id=null) {
        parent::__construct($id);
        
    }
    

    public function getStreetWithNumber() {
        return trim($this->getStreet() . ' ' . $this->getStreetNo());
    }
    
    public function save() {
        
        if (!$this->getCountryId())
            $this->setCountryId(148);
        
        return parent::save();
    }
    
}

