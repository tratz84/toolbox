<?php


namespace invoice\model;


class OrderStatus extends base\OrderStatusBase {

	public function __construct($id=null) {
		parent::__construct( $id );
		
		$this->setActive( true );
		
	}
}

