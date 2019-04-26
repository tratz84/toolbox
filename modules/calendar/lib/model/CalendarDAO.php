<?php


namespace calendar\model;


class CalendarDAO extends \core\db\DAOObject {

	public function __construct() {
		$this->setResource( 'default' );
		$this->setObjectName( '\\calendar\\model\\Calendar' );
	}
	
	
	
	public function readAll() { 
	    return $this->queryList("select * from cal__calendar where deleted is null");
	}
	
	public function readActive() {
	    return $this->queryList("select * from cal__calendar where deleted is null and active = true");
	}
	
	public function read($id) {
	    $l = $this->queryList("select * from cal__calendar where calendar_id = ? and deleted is null", array($id));
	    
	    if (count($l)) {
	        return $l[0];
	    } else {
	        return null;
	    }
	}
	
	public function markDeleted($id) {
	    $this->query('update cal__calendar set deleted = now() where calendar_id = ?', array($id));
	}

}

