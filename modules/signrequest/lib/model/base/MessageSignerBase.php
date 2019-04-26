<?php


namespace signrequest\model\base;


class MessageSignerBase extends \core\db\DBObject {

	public function __construct($id=null) {
		$this->setResource( 'default' );
		$this->setTableName( 'signrequest__message_signer' );
		$this->setPrimaryKey( 'message_signer_id' );
		$this->setDatabaseFields( array (
  'message_signer_id' => 
  array (
    'Field' => 'message_signer_id',
    'Type' => 'int(11)',
    'Null' => 'NO',
    'Key' => 'PRI',
    'Default' => NULL,
    'Extra' => 'auto_increment',
  ),
  'message_id' => 
  array (
    'Field' => 'message_id',
    'Type' => 'int(11)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
  ),
  'signer_email' => 
  array (
    'Field' => 'signer_email',
    'Type' => 'varchar(255)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
  ),
  'signer_name' => 
  array (
    'Field' => 'signer_name',
    'Type' => 'varchar(255)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
  ),
) );
		
		if ($id != null)
			$this->setField($this->primaryKey, $id);
	}
	
		
	public function setMessageSignerId($p) { $this->setField('message_signer_id', $p); }
	public function getMessageSignerId() { return $this->getField('message_signer_id'); }
	
		
	public function setMessageId($p) { $this->setField('message_id', $p); }
	public function getMessageId() { return $this->getField('message_id'); }
	
		
	public function setSignerEmail($p) { $this->setField('signer_email', $p); }
	public function getSignerEmail() { return $this->getField('signer_email'); }
	
		
	public function setSignerName($p) { $this->setField('signer_name', $p); }
	public function getSignerName() { return $this->getField('signer_name'); }
	
	
}

