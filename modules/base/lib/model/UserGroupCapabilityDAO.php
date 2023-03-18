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
	
	
	public function readByUser($userId) {
	    $sql = "select ugc.*
                from base__user_group_capability ugc 
                join base__user_group_user ugu on (ugu.user_group_id = ugc.user_group_id)
                where ugu.user_id=?";
	    
	    return $this->queryList($sql, array($userId));
	}
	
	
}

