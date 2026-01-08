<?php


namespace securemail\model;


class SecureMessageLogDAO extends \core\db\DAOObject {

	public function __construct() {
		$this->setResource( 'default' );
		$this->setObjectName( '\\securemail\\model\\SecureMessageLog' );
	}
	
	
	
	public function readByMessage($secureMessageId) {
	    $sql = "select * from securemail__secure_message_log where secure_message_id = ? order by secure_message_log_id desc";
	    
	    return $this->queryList($sql, array($secureMessageId));
	}
	

}

