<?php


namespace base\model;


class UserGroupUserDAO extends \core\db\DAOObject {

	public function __construct() {
		$this->setResource( 'default' );
		$this->setObjectName( '\\base\\model\\UserGroupUser' );
	}
	
	public function deleteByGroup($groupId) {
	    return $this->query('delete from base__user_group_user where user_group_id = ?', array($groupId));
	}
	
	public function deleteByUser( $userId ) {
	    return $this->query('delete from base__user_group_user where user_id = ?', array($userId));
	}
	
	
	public function readByUser( $userId ) {
	    $sql = "SELECT *
                FROM base__user_group_user
                where user_id=?";
	    
	    return $this->queryList( $sql, array($userId) );
	}

}

