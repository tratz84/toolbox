<?php


namespace payment\model;


class PaymentImportDAO extends \core\db\DAOObject {

	public function __construct() {
		$this->setResource( 'default' );
		$this->setObjectName( '\\payment\\model\\PaymentImport' );
	}
	

}

