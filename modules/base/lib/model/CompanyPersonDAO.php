<?php


namespace base\model;


class CompanyPersonDAO extends \core\db\DAOObject {

	public function __construct() {
		$this->setResource( 'default' );
		$this->setObjectName( '\\base\\model\\CompanyPerson' );
	}
	

}

