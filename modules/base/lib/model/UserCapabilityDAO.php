<?php


namespace base\model;


class UserCapabilityDAO extends \core\db\DAOObject {

	public function __construct() {
		$this->setResource( 'default' );
		$this->setObjectName( '\\base\\model\\UserCapability' );
	}
	
	
	public function readByUser($userId) {
	    return $this->queryList("select * from base__user_capability where user_id = ?", array($userId));
	}

	public function deleteByUser($userId) {
	    return $this->query("delete from base__user_capability where user_id = ?", array($userId));
	}
	
}

