<?php


namespace mollie\model;


class MolliePayment extends base\MolliePaymentBase {

	public function __construct($id=null) {
		parent::__construct( $id );
		
	}
	
	
	
	public function generateUid() {
	    $t = md5( uniqid() . time() . uniqid() );
	    
	    $this->setUid( $t );
	}
}

