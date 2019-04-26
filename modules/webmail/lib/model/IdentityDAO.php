<?php


namespace webmail\model;


class IdentityDAO extends \core\db\DAOObject {

	public function __construct() {
		$this->setResource( 'default' );
		$this->setObjectName( '\\webmail\\model\\Identity' );
	}
	
	
	public function read($id) {
	    $l = $this->queryList("select * from webmail__identity where identity_id = ?", array($id));
	    
	    if (count($l)) {
	        return $l[0];
	    } else {
	        return null;
	    }
	}
	
	
	public function readAll() { 
	    return $this->queryList("select * from webmail__identity order by sort, identity_id");
	}

	public function readActive() {
	    return $this->queryList("select * from webmail__identity where active = true order by sort, identity_id");
	}
	
	public function delete($id) {
	    $this->query("delete from webmail__identity where identity_id = ?", array($id));
	}
	
}

