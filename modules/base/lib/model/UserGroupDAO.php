<?php


namespace base\model;


class UserGroupDAO extends \core\db\DAOObject {

	public function __construct() {
		$this->setResource( 'default' );
		$this->setObjectName( '\\base\\model\\UserGroup' );
	}
	
	
	public function read($id) {
		return $this->queryOne('select * from base__user_group where user_group_id=?', array($id));
	}
	
	
	public function readAll() {
	    $sql = "select *
                from base__user_group
                order by parent_user_group_id, sort";
	    
	    return $this->queryList( $sql );
	}

	
}

