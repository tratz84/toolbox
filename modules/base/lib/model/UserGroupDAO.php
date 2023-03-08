<?php


namespace base\model;


class UserGroupDAO extends \core\db\DAOObject {

	public function __construct() {
		$this->setResource( 'default' );
		$this->setObjectName( '\\base\\model\\UserGroup' );
	}
	

}

