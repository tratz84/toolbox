<?php


namespace base\model;


class UserGroupUserDAO extends \core\db\DAOObject {

	public function __construct() {
		$this->setResource( 'default' );
		$this->setObjectName( '\\base\\model\\UserGroupUser' );
	}
	

}

