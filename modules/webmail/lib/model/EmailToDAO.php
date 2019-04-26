<?php


namespace webmail\model;


class EmailToDAO extends \core\db\DAOObject {

	public function __construct() {
		$this->setResource( 'default' );
		$this->setObjectName( '\\webmail\\model\\EmailTo' );
	}
	
	
	public function readByEmail($emailId) {
	    return $this->queryList("select * from webmail__email_to where email_id = ?", array($emailId));
	}

}

