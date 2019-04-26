<?php


namespace signrequest\model;


class MessageDAO extends \core\db\DAOObject {

	public function __construct() {
		$this->setResource( 'default' );
		$this->setObjectName( '\\signrequest\\model\\Message' );
	}
	
	
	public function read($id) {
	    $l = $this->queryList("select * from signrequest__message where message_id = ?", array($id));
	    
	    if (count($l)) {
	        return $l[0];
	    } else {
	        return null;
	    }
	}
	
	
	public function getCountByRef($refObject, $refId) {
	    
	    return $this->queryValue("select count(*) from signrequest__message where ref_object = ? and ref_id = ?", array($refObject, $refId));
	}

	public function getSentCountByRef($refObject, $refId) {
	    
	    return $this->queryValue("select count(*) from signrequest__message where sent = true and ref_object = ? and ref_id = ?", array($refObject, $refId));
	}
}

