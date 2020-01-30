<?php


namespace payment\model;


class PaymentDAO extends \core\db\DAOObject {

	public function __construct() {
		$this->setResource( 'default' );
		$this->setObjectName( '\\payment\\model\\Payment' );
	}
	

}

