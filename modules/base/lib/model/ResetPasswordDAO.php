<?php


namespace base\model;


class ResetPasswordDAO extends \core\db\DAOObject {

	public function __construct() {
		$this->setResource( 'default' );
		$this->setObjectName( '\\base\\model\\ResetPassword' );
	}
	
	
	
	public function resetPasswordCount( $ip, $time_sec=null ) {
	    $params = array();
	    $sql = "select count(*)
                FROM base__reset_password
                WHERE request_ip = ? ";
	    $params[] = $ip;
	    
	    if ($time_sec) {
	        $sql .= " AND TIME_TO_SEC(TIMEDIFF(now(), created)) <= ?";
	        $params[] = $time_sec;
	    }
	    
	    return $this->queryValue( $sql, $params );
	}
	
	
	public function read($id, $securityString=null) {
	    $params = array();
	    $sql = "select *
                from base__reset_password
                where reset_password_id = ?";
	    $params[] = $id;
	    
	    if ($securityString) {
	        $sql .= ' and security_string = ?';
	        $params[] = $securityString;
	    }
	    
	    return $this->queryOne($sql, $params);
	}
	
	

}

