<?php


namespace admin\model;


class AutologinDAO extends \core\db\DAOObject {

	public function __construct() {
		$this->setResource( 'admin' );
		$this->setObjectName( '\\admin\\model\\Autologin' );
	}
	
	
	public function readBySecurityString($autologinId, $securityString) {
	    $sql = "select * from toolbox__autologin where securityString = ? and autologin_id = ?";
	    
	    $l = $this->queryList($sql, array($securityString, $autologinId));
	    
	    return $l;
	}
	
	public function deleteByUsername($contextName, $username) {
	    $sql = "delete from toolbox__autologin where contextName=? and username=?";
	    
	    return $this->query($sql, array($contextName, $username));
	}

}

