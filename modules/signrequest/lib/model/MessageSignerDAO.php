<?php


namespace signrequest\model;


class MessageSignerDAO extends \core\db\DAOObject {

	public function __construct() {
		$this->setResource( 'default' );
		$this->setObjectName( '\\signrequest\\model\\MessageSigner' );
	}
	
	
	public function readByMessage($messageId) {
	    $l = $this->queryList("select * from signrequest__message_signer where message_id = ?", array($messageId));
	    
	    return $l;
	}

}

