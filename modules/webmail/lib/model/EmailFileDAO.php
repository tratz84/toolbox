<?php


namespace webmail\model;


class EmailFileDAO extends \core\db\DAOObject {

	public function __construct() {
		$this->setResource( 'default' );
		$this->setObjectName( '\\webmail\\model\\EmailFile' );
	}
	
	
	public function readByEmail($emailId) {
	    return $this->queryList("select * from webmail__email_file where email_id = ?", array($emailId));
	}
	
	public function read($id) {
	    $l = $this->queryList("select * from webmail__email_file where email_file_id = ?", array($id));
	    
	    if (count($l)) {
	        return $l[0];
	    } else {
	        return null;
	    }
	}

}

