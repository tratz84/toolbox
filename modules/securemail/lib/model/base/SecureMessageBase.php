<?php


namespace securemail\model\base;


class SecureMessageBase extends \core\db\DBObject {

	public function __construct($id=null) {
		$this->setResource( 'default' );
		$this->setTableName( 'securemail__secure_message' );
		$this->setPrimaryKey( 'secure_message_id' );
		$this->setDatabaseFields( array (
  'secure_message_id' => 
  array (
    'Field' => 'secure_message_id',
    'Type' => 'int',
    'Null' => 'NO',
    'Key' => 'PRI',
    'Default' => NULL,
    'Extra' => 'auto_increment',
  ),
  'encryption_algo' => 
  array (
    'Field' => 'encryption_algo',
    'Type' => 'varchar(16)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
  ),
  'password_hash' => 
  array (
    'Field' => 'password_hash',
    'Type' => 'varchar(128)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
  ),
  'ttl_type' => 
  array (
    'Field' => 'ttl_type',
    'Type' => 'varchar(16)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
  ),
  'ttl_expires_on' => 
  array (
    'Field' => 'ttl_expires_on',
    'Type' => 'datetime',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
  ),
  'short_description' => 
  array (
    'Field' => 'short_description',
    'Type' => 'text',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
  ),
  'encrypted_message' => 
  array (
    'Field' => 'encrypted_message',
    'Type' => 'longtext',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
  ),
  'deleted' => 
  array (
    'Field' => 'deleted',
    'Type' => 'datetime',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
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
  'person_id' => 
  array (
    'Field' => 'person_id',
    'Type' => 'int',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
  ),
  'company_id' => 
  array (
    'Field' => 'company_id',
    'Type' => 'int',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
  ),
  'password' => 
  array (
    'Field' => 'password',
    'Type' => 'varchar(128)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
  ),
  'encryption_iv' => 
  array (
    'Field' => 'encryption_iv',
    'Type' => 'text',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
  ),
  'view_method' => 
  array (
    'Field' => 'view_method',
    'Type' => 'varchar(16)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
  ),
) );
		
		if ($id != null)
			$this->setField($this->primaryKey, $id);
	}
	
		
	public function setSecureMessageId($p) { $this->setField('secure_message_id', $p); }
	public function getSecureMessageId() { return $this->getField('secure_message_id'); }
	
		
	public function setEncryptionAlgo($p) { $this->setField('encryption_algo', $p); }
	public function getEncryptionAlgo() { return $this->getField('encryption_algo'); }
	
		
	public function setPasswordHash($p) { $this->setField('password_hash', $p); }
	public function getPasswordHash() { return $this->getField('password_hash'); }
	
		
	public function setTtlType($p) { $this->setField('ttl_type', $p); }
	public function getTtlType() { return $this->getField('ttl_type'); }
	
		
	public function setTtlExpiresOn($p) { $this->setField('ttl_expires_on', $p); }
	public function getTtlExpiresOn() { return $this->getField('ttl_expires_on'); }
	
		
	public function setShortDescription($p) { $this->setField('short_description', $p); }
	public function getShortDescription() { return $this->getField('short_description'); }
	
		
	public function setEncryptedMessage($p) { $this->setField('encrypted_message', $p); }
	public function getEncryptedMessage() { return $this->getField('encrypted_message'); }
	
		
	public function setDeleted($p) { $this->setField('deleted', $p); }
	public function getDeleted() { return $this->getField('deleted'); }
	
		
	public function setEdited($p) { $this->setField('edited', $p); }
	public function getEdited() { return $this->getField('edited'); }
	
		
	public function setCreated($p) { $this->setField('created', $p); }
	public function getCreated() { return $this->getField('created'); }
	
		
	public function setPersonId($p) { $this->setField('person_id', $p); }
	public function getPersonId() { return $this->getField('person_id'); }
	
		
	public function setCompanyId($p) { $this->setField('company_id', $p); }
	public function getCompanyId() { return $this->getField('company_id'); }
	
		
	public function setPassword($p) { $this->setField('password', $p); }
	public function getPassword() { return $this->getField('password'); }
	
		
	public function setEncryptionIv($p) { $this->setField('encryption_iv', $p); }
	public function getEncryptionIv() { return $this->getField('encryption_iv'); }
	
		
	public function setViewMethod($p) { $this->setField('view_method', $p); }
	public function getViewMethod() { return $this->getField('view_method'); }
	
	
}

