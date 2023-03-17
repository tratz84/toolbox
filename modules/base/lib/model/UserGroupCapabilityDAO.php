<?php


namespace base\model;


class UserGroupCapabilityDAO extends \core\db\DAOObject {

	public function __construct() {
		$this->setResource( 'default' );
		$this->setObjectName( '\\base\\model\\UserGroupCapability' );
	}

	
	public function readByUserGroup( $userGroupId ) {
	    return $this->queryList('select * from base__user_group_capability where user_group_id = ?', array($userGroupId));
	}

	
	public function deleteByGroup($groupId) {
	    return $this->query('delete from base__user_group_capability where user_group_id = ?', array($groupId));
	}
}

