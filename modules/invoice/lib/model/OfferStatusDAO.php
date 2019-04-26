<?php


namespace invoice\model;


class OfferStatusDAO extends \core\db\DAOObject {

	public function __construct() {
		$this->setResource( 'default' );
		$this->setObjectName( '\\invoice\\model\\OfferStatus' );
	}
	

	public function readAll() {
	    $sql = "select * from invoice__offer_status order by sort";
	    
	    return $this->queryList($sql);
	}
	
	public function readActive() {
	    $sql = "select * from invoice__offer_status where active = true order by sort";
	    
	    return $this->queryList($sql);
	}
	
	
	public function read($id) {
	    $l = $this->queryList("select * from invoice__offer_status where offer_status_id = ?", array($id));
	    
	    if (count($l)) {
	        return $l[0];
	    } else {
	        return null;
	    }
	}

	public function readFirst() {
	    $sql = "select * from invoice__offer_status order by sort limit 1";
	    
	    return $this->queryOne($sql);
	}
	
	public function readByDefaultStatus() {
	    $sql = "select * from invoice__offer_status where default_selected = true order by sort limit 1";
	    
	    return $this->queryOne($sql);
	}
	
	public function delete($id) {
	    return $this->query("delete from invoice__offer_status where offer_status_id = ?", array($id));
	}
}

