<?php


namespace base\model;


class UserIpDAO extends \core\db\DAOObject {

	public function __construct() {
		$this->setResource( 'default' );
		$this->setObjectName( '\\base\\model\\UserIp' );
	}
	
	
	public function readByUser($userId) {
	    return $this->queryList("select * from base__user_ip where user_id = ?", array($userId));
	}

	public function deleteByUser($userId) {
	    $this->query('delete from base__user_ip where user_id = ?', array($userId));
	}
	
}

