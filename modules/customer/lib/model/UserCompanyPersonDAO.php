<?php


namespace customer\model;


class UserCompanyPersonDAO extends \core\db\DAOObject {

	public function __construct() {
		$this->setResource( 'default' );
		$this->setObjectName( '\\customer\\model\\UserCompanyPerson' );
	}
	

}

