<?php


namespace invoice\model;


class OfferStatus extends base\OfferStatusBase {

    public function __construct($id=null) {
        parent::__construct($id);
        
        $this->setActive(true);
    }

    
    public function __toString() {
        return $this->getDescription();
    }
    
    
}

