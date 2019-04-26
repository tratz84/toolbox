<?php


namespace invoice\model;


class InvoiceStatus extends base\InvoiceStatusBase {

    public function __construct($id=null) {
        parent::__construct($id);
        
        $this->setActive(true);
    }
    
    
    public function __toString() {
        return $this->getDescription();
    }

}

