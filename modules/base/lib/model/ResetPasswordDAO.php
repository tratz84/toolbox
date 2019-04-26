<?php


namespace base\model;


class ResetPasswordDAO extends \core\db\DAOObject {

	public function __construct() {
		$this->setResource( 'default' );
		$this->setObjectName( '\\base\\model\\ResetPassword' );
	}
	

}

