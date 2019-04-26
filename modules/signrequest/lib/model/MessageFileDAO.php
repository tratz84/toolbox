<?php


namespace signrequest\model;


class MessageFileDAO extends \core\db\DAOObject {

	public function __construct() {
		$this->setResource( 'default' );
		$this->setObjectName( '\\signrequest\\model\\MessageFile' );
	}
	
	
	public function readByMessage($messageId) {
	    $sql = "select * from signrequest__message_file where message_id = ?";
	    
	    return $this->queryList($sql, array($messageId));
	}

	public function readFile($messageFileId, $messageId=null) {
	    $params = array();
	    
	    $sql = "select * from signrequest__message_file where message_file_id = ? ";
	    $params[] = $messageFileId;
	    
	    if ($messageId) {
	        $sql .= ' and message_id = ? ';
	        $params[] = $messageId;
	    }
	    
	    return $this->queryOne($sql, $params);
	}
}

