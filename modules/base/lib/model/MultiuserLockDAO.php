<?php


namespace base\model;


class MultiuserLockDAO extends \core\db\DAOObject {

	public function __construct() {
		$this->setResource( 'default' );
		$this->setObjectName( '\\base\\model\\MultiuserLock' );
	}

	
	public function deleteByUsername($username) {
	    $this->query('delete from base__multiuser_lock where username = ?', array($username));
	}
	
	public function deleteByTab($username, $tabuid) {
	    $this->query('delete from base__multiuser_lock where username = ? and tabuid = ?', array($username, $tabuid));
	}
	
	public function readByTab($username, $tabuid) {
	    return $this->queryOne('select * from base__multiuser_lock where username = ? and tabuid = ?', array($username, $tabuid));
	}
	
	public function setLockKey($username, $tabuid, $val=null) {
	    $this->query('update base__multiuser_lock set lock_key = ? where username = ? and tabuid = ?', $val, $username, $tabuid);
	}
	
	
	public function cleanup() {
	    $ci = 15;
	    if (defined('MULTIUSER_CHECK_INTERVAL')) {
	        $ci = intval(MULTIUSER_CHECK_INTERVAL) + 5;
	    }
	    
	    $this->query("delete from base__multiuser_lock where created < now() - interval {$ci} second");
// 	    $this->query("optimize table base__multiuser_lock");
	}
	
	public function lockCountByUsername($lockKey) {
	    $res = $this->query('select username, count(*) from base__multiuser_lock where lock_key = ? group by username', array($lockKey));
	    
	    $l = array();
	    while ($r = $res->fetch_array()) {
	        $username = $r[0];
	        $count = $r[1];
	        
	        $l[$username] = $count;
	    }
	    
	    return $l;
	}

}

