<?php


namespace webmail\model\base;


class WebmailAzureTokenBase extends \core\db\DBObject {

	public function __construct($id=null) {
		$this->setResource( 'default' );
		$this->setTableName( 'webmail__webmail_azure_token' );
		$this->setPrimaryKey( 'webmail_azure_token_id' );
		$this->setDatabaseFields( array (
  'webmail_azure_token_id' => 
  array (
    'Field' => 'webmail_azure_token_id',
    'Type' => 'int',
    'Null' => 'NO',
    'Key' => 'PRI',
    'Default' => NULL,
    'Extra' => 'auto_increment',
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
  'request_data' => 
  array (
    'Field' => 'request_data',
    'Type' => 'blob',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
  ),
  'response_data' => 
  array (
    'Field' => 'response_data',
    'Type' => 'blob',
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
  'token_type' => 
  array (
    'Field' => 'token_type',
    'Type' => 'varchar(16)',
    'Null' => 'YES',
    'Key' => '',
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
) );
		
		if ($id != null)
			$this->setField($this->primaryKey, $id);
	}
	
		
	public function setWebmailAzureTokenId($p) { $this->setField('webmail_azure_token_id', $p); }
	public function getWebmailAzureTokenId() { return $this->getField('webmail_azure_token_id'); }
	
		
	public function setConnectorId($p) { $this->setField('connector_id', $p); }
	public function getConnectorId() { return $this->getField('connector_id'); }
	
		
	public function setRequestData($p) { $this->setField('request_data', $p); }
	public function getRequestData() { return $this->getField('request_data'); }
	
		
	public function setResponseData($p) { $this->setField('response_data', $p); }
	public function getResponseData() { return $this->getField('response_data'); }
	
		
	public function setCreated($p) { $this->setField('created', $p); }
	public function getCreated() { return $this->getField('created'); }
	
		
	public function setTokenType($p) { $this->setField('token_type', $p); }
	public function getTokenType() { return $this->getField('token_type'); }
	
		
	public function setDescription($p) { $this->setField('description', $p); }
	public function getDescription() { return $this->getField('description'); }
	
	
}

