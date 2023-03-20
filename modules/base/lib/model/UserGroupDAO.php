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

	
	public function readByUser( $userId ) {
	    $sql = "SELECT ug.*
                FROM base__user_group ug
                join base__user_group_user ugu on (ug.user_group_id = ugu.user_group_id)
                where ugu.user_id=?";
	    
	    return $this->queryList( $sql, array($userId) );
	}
	
	
	public function searchForSelect( $q, $top=10 ) {
	    $sql = "select *
                from base__user_group g
                where group_name like ?
                limit ".intval($top);
	    
	    return $this->queryList( $sql, array('%'.$q.'%') );
	}
	
}

