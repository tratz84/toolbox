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
    'Type' => 'mediumblob',
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
  'azure_client_id' => 
  array (
    'Field' => 'azure_client_id',
    'Type' => 'varchar(128)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
  ),
  'azure_client_secret' => 
  array (
    'Field' => 'azure_client_secret',
    'Type' => 'varchar(128)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
  ),
  'azure_authorization_url' => 
  array (
    'Field' => 'azure_authorization_url',
    'Type' => 'varchar(512)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
  ),
  'azure_token_url' => 
  array (
    'Field' => 'azure_token_url',
    'Type' => 'varchar(512)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
  ),
  'refresh_timestamp' => 
  array (
    'Field' => 'refresh_timestamp',
    'Type' => 'datetime',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
  ),
  'expires_in' => 
  array (
    'Field' => 'expires_in',
    'Type' => 'int',
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
	
		
	public function setAzureClientId($p) { $this->setField('azure_client_id', $p); }
	public function getAzureClientId() { return $this->getField('azure_client_id'); }
	
		
	public function setAzureClientSecret($p) { $this->setField('azure_client_secret', $p); }
	public function getAzureClientSecret() { return $this->getField('azure_client_secret'); }
	
		
	public function setAzureAuthorizationUrl($p) { $this->setField('azure_authorization_url', $p); }
	public function getAzureAuthorizationUrl() { return $this->getField('azure_authorization_url'); }
	
		
	public function setAzureTokenUrl($p) { $this->setField('azure_token_url', $p); }
	public function getAzureTokenUrl() { return $this->getField('azure_token_url'); }
	
		
	public function setRefreshTimestamp($p) { $this->setField('refresh_timestamp', $p); }
	public function getRefreshTimestamp() { return $this->getField('refresh_timestamp'); }
	
		
	public function setExpiresIn($p) { $this->setField('expires_in', $p); }
	public function getExpiresIn() { return $this->getField('expires_in'); }
	
	
}

