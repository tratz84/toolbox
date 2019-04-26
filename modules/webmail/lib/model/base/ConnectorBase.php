<?php


namespace webmail\model\base;


class ConnectorBase extends \core\db\DBObject {

	public function __construct($id=null) {
		$this->setResource( 'default' );
		$this->setTableName( 'webmail__connector' );
		$this->setPrimaryKey( 'connector_id' );
		$this->setDatabaseFields( array (
  'connector_id' => 
  array (
    'Field' => 'connector_id',
    'Type' => 'int(11)',
    'Null' => 'NO',
    'Key' => 'PRI',
    'Default' => NULL,
    'Extra' => 'auto_increment',
  ),
  'user_id' => 
  array (
    'Field' => 'user_id',
    'Type' => 'int(11)',
    'Null' => 'YES',
    'Key' => 'MUL',
    'Default' => NULL,
    'Extra' => '',
  ),
  'description' => 
  array (
    'Field' => 'description',
    'Type' => 'varchar(255)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
  ),
  'connector_type' => 
  array (
    'Field' => 'connector_type',
    'Type' => 'varchar(16)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
  ),
  'hostname' => 
  array (
    'Field' => 'hostname',
    'Type' => 'varchar(255)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
  ),
  'port' => 
  array (
    'Field' => 'port',
    'Type' => 'int(11)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
  ),
  'username' => 
  array (
    'Field' => 'username',
    'Type' => 'varchar(255)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
  ),
  'password' => 
  array (
    'Field' => 'password',
    'Type' => 'varchar(255)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
  ),
  'nextrun_fullimport' => 
  array (
    'Field' => 'nextrun_fullimport',
    'Type' => 'tinyint(1)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
  ),
  'sent_connector_imapfolder_id' => 
  array (
    'Field' => 'sent_connector_imapfolder_id',
    'Type' => 'int(11)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
  ),
  'junk_connector_imapfolder_id' => 
  array (
    'Field' => 'junk_connector_imapfolder_id',
    'Type' => 'int(11)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
  ),
  'trash_connector_imapfolder_id' => 
  array (
    'Field' => 'trash_connector_imapfolder_id',
    'Type' => 'int(11)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
  ),
  'active' => 
  array (
    'Field' => 'active',
    'Type' => 'tinyint(1)',
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
) );
		
		if ($id != null)
			$this->setField($this->primaryKey, $id);
	}
	
		
	public function setConnectorId($p) { $this->setField('connector_id', $p); }
	public function getConnectorId() { return $this->getField('connector_id'); }
	
		
	public function setUserId($p) { $this->setField('user_id', $p); }
	public function getUserId() { return $this->getField('user_id'); }
	
		
	public function setDescription($p) { $this->setField('description', $p); }
	public function getDescription() { return $this->getField('description'); }
	
		
	public function setConnectorType($p) { $this->setField('connector_type', $p); }
	public function getConnectorType() { return $this->getField('connector_type'); }
	
		
	public function setHostname($p) { $this->setField('hostname', $p); }
	public function getHostname() { return $this->getField('hostname'); }
	
		
	public function setPort($p) { $this->setField('port', $p); }
	public function getPort() { return $this->getField('port'); }
	
		
	public function setUsername($p) { $this->setField('username', $p); }
	public function getUsername() { return $this->getField('username'); }
	
		
	public function setPassword($p) { $this->setField('password', $p); }
	public function getPassword() { return $this->getField('password'); }
	
		
	public function setNextrunFullimport($p) { $this->setField('nextrun_fullimport', $p); }
	public function getNextrunFullimport() { return $this->getField('nextrun_fullimport'); }
	
		
	public function setSentConnectorImapfolderId($p) { $this->setField('sent_connector_imapfolder_id', $p); }
	public function getSentConnectorImapfolderId() { return $this->getField('sent_connector_imapfolder_id'); }
	
		
	public function setJunkConnectorImapfolderId($p) { $this->setField('junk_connector_imapfolder_id', $p); }
	public function getJunkConnectorImapfolderId() { return $this->getField('junk_connector_imapfolder_id'); }
	
		
	public function setTrashConnectorImapfolderId($p) { $this->setField('trash_connector_imapfolder_id', $p); }
	public function getTrashConnectorImapfolderId() { return $this->getField('trash_connector_imapfolder_id'); }
	
		
	public function setActive($p) { $this->setField('active', $p); }
	public function getActive() { return $this->getField('active'); }
	
		
	public function setEdited($p) { $this->setField('edited', $p); }
	public function getEdited() { return $this->getField('edited'); }
	
		
	public function setCreated($p) { $this->setField('created', $p); }
	public function getCreated() { return $this->getField('created'); }
	
	
}

