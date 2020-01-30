<?php


namespace payment\model;


class PaymentRefDAO extends \core\db\DAOObject {

	public function __construct() {
		$this->setResource( 'default' );
		$this->setObjectName( '\\payment\\model\\PaymentRef' );
	}
	

}

