<?php


namespace base\model;


class CronDAO extends \core\db\DAOObject {

	public function __construct() {
		$this->setResource( 'default' );
		$this->setObjectName( '\\base\\model\\Cron' );
	}
	
	public function read($id) {
	    return $this->queryOne('select * from base__cron where cron_id = ?', array($id));
	}
	
	
	public function readAll() {
	    return $this->queryList('select * from base__cron order by cron_name');
	}
	
	public function readByName($name) {
	    $sql = "select * from base__cron where cron_name = ?";
	    
	    return $this->queryOne($sql, array($name));
	}

}

