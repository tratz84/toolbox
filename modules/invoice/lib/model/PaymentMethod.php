<?php


namespace invoice\model;


class PaymentMethod extends base\PaymentMethodBase {

    
    public function __construct($id=null) {
        parent::__construct( $id );
        
        $this->setActive( true );
    }
    
}

