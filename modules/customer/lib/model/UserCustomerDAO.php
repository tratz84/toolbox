<?php


namespace customer\model;


class UserCustomerDAO extends \core\db\DAOObject {

	public function __construct() {
		$this->setResource( 'default' );
		$this->setObjectName( '\\customer\\model\\UserCustomer' );
	}
	

}

