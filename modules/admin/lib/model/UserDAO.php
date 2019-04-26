<?php


namespace admin\model;


class UserDAO extends \core\db\DAOObject {

	public function __construct() {
		$this->setResource( 'admin' );
		$this->setObjectName( '\\admin\\model\\User' );
	}
	
	public function read($id) {
	    return $this->queryOne("select * from insights__user where user_id = ?", array($id));
	}
	
	public function readAll() {
	    return $this->queryList("select * from insights__user ");
	}

	public function readByUsername($username) {
	    $l = $this->queryList("select * from insights__user where username = ?", array($username));
	    
	    if (count($l)) {
	        return $l[0];
	    } else {
	        return null;
	    }
	}
	
	
	public function delete($id) {
	    return $this->query("delete from insights__user where user_id = ?", array($id));
	}
	
	public function search($opts) {
	    
	    $sql = "select * from insights__user  ";
	    
	    $where = array();
	    $params = array();
	    
	    if (isset($opts['username']) && trim($opts['username']) != '') {
	        $where[] = "username LIKE ? ";
	        $params[] = '%'.$opts['username'].'%';
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
	
	public function updatePassword($userId, $pass) {
	    return $this->query("update insights__user set password=? where user_id = ?", array($pass, $userId));
	}
	
}

