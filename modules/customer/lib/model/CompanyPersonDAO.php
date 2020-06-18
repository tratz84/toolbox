<?php


namespace customer\model;


class CompanyPersonDAO extends \core\db\DAOObject {

	public function __construct() {
		$this->setResource( 'default' );
		$this->setObjectName( '\\customer\\model\\CompanyPerson' );
	}
	

}

