<?php


namespace base\model;


class UserDAO extends \core\db\DAOObject {

	public function __construct() {
		$this->setResource( 'default' );
		$this->setObjectName( '\\base\\model\\User' );
	}
	
	public function delete($userId) {
	    $this->query('delete from base__user where user_id = ?', array($userId));
	}
	
	public function readAll() {
	    return $this->queryList("select * from base__user");
	}
	
	public function search($opts=array()) {
	    
	    $sql = "select * from base__user ";
	    
	    $where = array();
	    $params = array();
	    
	    if (isset($opts['username']) && trim($opts['username']) != '') {
	        $where[] = "username LIKE ? ";
	        $params[] = '%'.$opts['username'].'%';
	    }
	    
	    if (isset($opts['email']) && trim($opts['email']) != '') {
	        $where[] = "email LIKE ? ";
	        $params[] = '%'.$opts['email'].'%';
	    }
	    
	    if (isset($opts['user_type']) && trim($opts['user_type']) != '') {
	        $where[] = "user_type LIKE ? ";
	        $params[] = '%'.$opts['user_type'].'%';
	    }
	    
	    
	    if (count($where)) {
	        $sql .= "where ( " . implode(") AND (", $where) . ") ";
	    }
	    
	    $sql .= 'order by username';
	    
	    return $this->queryCursor($sql, $params);
	}
	
	
	public function read($id) {
	    $l = $this->queryList("select * from base__user where user_id = ?", array($id));
	    
	    if (count($l)) {
	        return $l[0];
	    } else {
	        return null;
	    }
	}

	public function readByUsername($u) {
	    $l = $this->queryList("select * from base__user where username = ?", array($u));
	    
	    if (count($l)) {
	        return $l[0];
	    } else {
	        return null;
	    }
	}
	
	
	public function setAutologinToken($user_id, $token) {
	    $this->query("update base__user set autologin_token = ? where user_id = ?", array($token, $user_id));
	}
	
	
	public function readByAutologinToken($token) {
	    return $this->queryList("select * from base__user where autologin_token = ?", array($token));
	}
	
	public function resetAutologinToken($token) {
	    $this->query("update base__user set autologin_token = null where autologin_token = ?", array($token));
	}

}

