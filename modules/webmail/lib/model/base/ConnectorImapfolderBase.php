<?php


namespace webmail\model\base;


class ConnectorImapfolderBase extends \core\db\DBObject {

	public function __construct($id=null) {
		$this->setResource( 'default' );
		$this->setTableName( 'webmail__connector_imapfolder' );
		$this->setPrimaryKey( 'connector_imapfolder_id' );
		$this->setDatabaseFields( array (
  'connector_imapfolder_id' => 
  array (
    'Field' => 'connector_imapfolder_id',
    'Type' => 'int(11)',
    'Null' => 'NO',
    'Key' => 'PRI',
    'Default' => NULL,
    'Extra' => 'auto_increment',
  ),
  'connector_id' => 
  array (
    'Field' => 'connector_id',
    'Type' => 'int(11)',
    'Null' => 'YES',
    'Key' => 'MUL',
    'Default' => NULL,
    'Extra' => '',
  ),
  'folderName' => 
  array (
    'Field' => 'folderName',
    'Type' => 'varchar(255)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
  ),
  'attributes' => 
  array (
    'Field' => 'attributes',
    'Type' => 'int(11)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
  ),
  'outgoing' => 
  array (
    'Field' => 'outgoing',
    'Type' => 'tinyint(1)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
  ),
  'junk' => 
  array (
    'Field' => 'junk',
    'Type' => 'tinyint(1)',
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
	
		
	public function setConnectorImapfolderId($p) { $this->setField('connector_imapfolder_id', $p); }
	public function getConnectorImapfolderId() { return $this->getField('connector_imapfolder_id'); }
	
		
	public function setConnectorId($p) { $this->setField('connector_id', $p); }
	public function getConnectorId() { return $this->getField('connector_id'); }
	
		
	public function setFolderName($p) { $this->setField('folderName', $p); }
	public function getFolderName() { return $this->getField('folderName'); }
	
		
	public function setAttributes($p) { $this->setField('attributes', $p); }
	public function getAttributes() { return $this->getField('attributes'); }
	
		
	public function setOutgoing($p) { $this->setField('outgoing', $p); }
	public function getOutgoing() { return $this->getField('outgoing'); }
	
		
	public function setJunk($p) { $this->setField('junk', $p); }
	public function getJunk() { return $this->getField('junk'); }
	
		
	public function setActive($p) { $this->setField('active', $p); }
	public function getActive() { return $this->getField('active'); }
	
		
	public function setEdited($p) { $this->setField('edited', $p); }
	public function getEdited() { return $this->getField('edited'); }
	
		
	public function setCreated($p) { $this->setField('created', $p); }
	public function getCreated() { return $this->getField('created'); }
	
	
}

