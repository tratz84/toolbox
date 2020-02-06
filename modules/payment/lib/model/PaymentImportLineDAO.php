<?php


namespace payment\model;


class PaymentImportLineDAO extends \core\db\DAOObject {

	public function __construct() {
		$this->setResource( 'default' );
		$this->setObjectName( '\\payment\\model\\PaymentImportLine' );
	}
	

}

