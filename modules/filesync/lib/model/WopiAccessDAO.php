<?php


namespace filesync\model;


class WopiAccessDAO extends \core\db\DAOObject {

	public function __construct() {
		$this->setResource( 'default' );
		$this->setObjectName( '\\filesync\\model\\WopiAccess' );
	}
	
	
	
	public function read($id) {
	    return $this->queryOne('SELECT * FROM filesync__wopi_access where wopi_access_id = ?', array($id));
	}
	

}

