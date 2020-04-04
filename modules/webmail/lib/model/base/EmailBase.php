<?php


namespace webmail\model\base;


class EmailBase extends \core\db\DBObject {

	public function __construct($id=null) {
		$this->setResource( 'default' );
		$this->setTableName( 'webmail__email' );
		$this->setPrimaryKey( 'email_id' );
		$this->setDatabaseFields( array (
  'email_id' => 
  array (
    'Field' => 'email_id',
    'Type' => 'int',
    'Null' => 'NO',
    'Key' => 'PRI',
    'Default' => NULL,
    'Extra' => 'auto_increment',
  ),
  'user_id' => 
  array (
    'Field' => 'user_id',
    'Type' => 'int',
    'Null' => 'YES',
    'Key' => 'MUL',
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
  'person_id' => 
  array (
    'Field' => 'person_id',
    'Type' => 'int',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
  ),
  'identity_id' => 
  array (
    'Field' => 'identity_id',
    'Type' => 'int',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
  ),
  'connector_id' => 
  array (
    'Field' => 'connector_id',
    'Type' => 'int',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
  ),
  'connector_imapfolder_id' => 
  array (
    'Field' => 'connector_imapfolder_id',
    'Type' => 'int',
    'Null' => 'YES',
    'Key' => 'MUL',
    'Default' => NULL,
    'Extra' => '',
  ),
  'attributes' => 
  array (
    'Field' => 'attributes',
    'Type' => 'int',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
  ),
  'message_id' => 
  array (
    'Field' => 'message_id',
    'Type' => 'varchar(255)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
  ),
  'spam' => 
  array (
    'Field' => 'spam',
    'Type' => 'tinyint(1)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
  ),
  'incoming' => 
  array (
    'Field' => 'incoming',
    'Type' => 'tinyint(1)',
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
  'subject' => 
  array (
    'Field' => 'subject',
    'Type' => 'varchar(255)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
  ),
  'text_content' => 
  array (
    'Field' => 'text_content',
    'Type' => 'mediumtext',
    'Null' => 'YES',
    'Key' => 'MUL',
    'Default' => NULL,
    'Extra' => '',
  ),
  'received' => 
  array (
    'Field' => 'received',
    'Type' => 'datetime',
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
  'status' => 
  array (
    'Field' => 'status',
    'Type' => 'varchar(16)',
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
  'search_id' => 
  array (
    'Field' => 'search_id',
    'Type' => 'bigint',
    'Null' => 'YES',
    'Key' => 'MUL',
    'Default' => NULL,
    'Extra' => '',
  ),
  'solr_mail_id' => 
  array (
    'Field' => 'solr_mail_id',
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
	
		
	public function setEmailId($p) { $this->setField('email_id', $p); }
	public function getEmailId() { return $this->getField('email_id'); }
	
		
	public function setUserId($p) { $this->setField('user_id', $p); }
	public function getUserId() { return $this->getField('user_id'); }
	
		
	public function setCompanyId($p) { $this->setField('company_id', $p); }
	public function getCompanyId() { return $this->getField('company_id'); }
	
		
	public function setPersonId($p) { $this->setField('person_id', $p); }
	public function getPersonId() { return $this->getField('person_id'); }
	
		
	public function setIdentityId($p) { $this->setField('identity_id', $p); }
	public function getIdentityId() { return $this->getField('identity_id'); }
	
		
	public function setConnectorId($p) { $this->setField('connector_id', $p); }
	public function getConnectorId() { return $this->getField('connector_id'); }
	
		
	public function setConnectorImapfolderId($p) { $this->setField('connector_imapfolder_id', $p); }
	public function getConnectorImapfolderId() { return $this->getField('connector_imapfolder_id'); }
	
		
	public function setAttributes($p) { $this->setField('attributes', $p); }
	public function getAttributes() { return $this->getField('attributes'); }
	
		
	public function setMessageId($p) { $this->setField('message_id', $p); }
	public function getMessageId() { return $this->getField('message_id'); }
	
		
	public function setSpam($p) { $this->setField('spam', $p); }
	public function getSpam() { return $this->getField('spam'); }
	
		
	public function setIncoming($p) { $this->setField('incoming', $p); }
	public function getIncoming() { return $this->getField('incoming'); }
	
		
	public function setFromName($p) { $this->setField('from_name', $p); }
	public function getFromName() { return $this->getField('from_name'); }
	
		
	public function setFromEmail($p) { $this->setField('from_email', $p); }
	public function getFromEmail() { return $this->getField('from_email'); }
	
		
	public function setSubject($p) { $this->setField('subject', $p); }
	public function getSubject() { return $this->getField('subject'); }
	
		
	public function setTextContent($p) { $this->setField('text_content', $p); }
	public function getTextContent() { return $this->getField('text_content'); }
	
		
	public function setReceived($p) { $this->setField('received', $p); }
	public function getReceived() { return $this->getField('received'); }
	
		
	public function setDeleted($p) { $this->setField('deleted', $p); }
	public function getDeleted() { return $this->getField('deleted'); }
	
		
	public function setStatus($p) { $this->setField('status', $p); }
	public function getStatus() { return $this->getField('status'); }
	
		
	public function setCreated($p) { $this->setField('created', $p); }
	public function getCreated() { return $this->getField('created'); }
	
		
	public function setSearchId($p) { $this->setField('search_id', $p); }
	public function getSearchId() { return $this->getField('search_id'); }
	
		
	public function setSolrMailId($p) { $this->setField('solr_mail_id', $p); }
	public function getSolrMailId() { return $this->getField('solr_mail_id'); }
	
	
}

