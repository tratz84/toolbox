<?php


namespace signrequest\model\base;


class MessageBase extends \core\db\DBObject {

	public function __construct($id=null) {
		$this->setResource( 'default' );
		$this->setTableName( 'signrequest__message' );
		$this->setPrimaryKey( 'message_id' );
		$this->setDatabaseFields( array (
  'message_id' => 
  array (
    'Field' => 'message_id',
    'Type' => 'int(11)',
    'Null' => 'NO',
    'Key' => 'PRI',
    'Default' => NULL,
    'Extra' => 'auto_increment',
  ),
  'ref_object' => 
  array (
    'Field' => 'ref_object',
    'Type' => 'varchar(32)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
  ),
  'ref_id' => 
  array (
    'Field' => 'ref_id',
    'Type' => 'int(11)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
  ),
  'from_name' => 
  array (
    'Field' => 'from_name',
    'Type' => 'varchar(255)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
  ),
  'from_email' => 
  array (
    'Field' => 'from_email',
    'Type' => 'varchar(255)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
  ),
  'message' => 
  array (
    'Field' => 'message',
    'Type' => 'text',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
  ),
  'documents_response' => 
  array (
    'Field' => 'documents_response',
    'Type' => 'text',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
  ),
  'signrequests_response' => 
  array (
    'Field' => 'signrequests_response',
    'Type' => 'text',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
  ),
  'sent' => 
  array (
    'Field' => 'sent',
    'Type' => 'tinyint(1)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => '0',
    'Extra' => '',
  ),
  'edited' => 
  array (
    'Field' => 'edited',
    'Type' => 'datetime',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
  ),
  'created' => 
  array (
    'Field' => 'created',
    'Type' => 'datetime',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
  ),
) );
		
		if ($id != null)
			$this->setField($this->primaryKey, $id);
	}
	
		
	public function setMessageId($p) { $this->setField('message_id', $p); }
	public function getMessageId() { return $this->getField('message_id'); }
	
		
	public function setRefObject($p) { $this->setField('ref_object', $p); }
	public function getRefObject() { return $this->getField('ref_object'); }
	
		
	public function setRefId($p) { $this->setField('ref_id', $p); }
	public function getRefId() { return $this->getField('ref_id'); }
	
		
	public function setFromName($p) { $this->setField('from_name', $p); }
	public function getFromName() { return $this->getField('from_name'); }
	
		
	public function setFromEmail($p) { $this->setField('from_email', $p); }
	public function getFromEmail() { return $this->getField('from_email'); }
	
		
	public function setMessage($p) { $this->setField('message', $p); }
	public function getMessage() { return $this->getField('message'); }
	
		
	public function setDocumentsResponse($p) { $this->setField('documents_response', $p); }
	public function getDocumentsResponse() { return $this->getField('documents_response'); }
	
		
	public function setSignrequestsResponse($p) { $this->setField('signrequests_response', $p); }
	public function getSignrequestsResponse() { return $this->getField('signrequests_response'); }
	
		
	public function setSent($p) { $this->setField('sent', $p); }
	public function getSent() { return $this->getField('sent'); }
	
		
	public function setEdited($p) { $this->setField('edited', $p); }
	public function getEdited() { return $this->getField('edited'); }
	
		
	public function setCreated($p) { $this->setField('created', $p); }
	public function getCreated() { return $this->getField('created'); }
	
	
}

