<?php


namespace filesync\model\base;


class WopiAccessBase extends \core\db\DBObject {

	public function __construct($id=null) {
		$this->setResource( 'default' );
		$this->setTableName( 'filesync__wopi_access' );
		$this->setPrimaryKey( 'wopi_access_id' );
		$this->setDatabaseFields( array (
  'wopi_access_id' => 
  array (
    'Field' => 'wopi_access_id',
    'Type' => 'int',
    'Null' => 'NO',
    'Key' => 'PRI',
    'Default' => NULL,
    'Extra' => 'auto_increment',
  ),
  'access_token' => 
  array (
    'Field' => 'access_token',
    'Type' => 'varchar(1024)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
  ),
  'access_token_ttl' => 
  array (
    'Field' => 'access_token_ttl',
    'Type' => 'int',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
  ),
  'path' => 
  array (
    'Field' => 'path',
    'Type' => 'varchar(1024)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
  ),
  'user_id' => 
  array (
    'Field' => 'user_id',
    'Type' => 'int',
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
	
		
	public function setWopiAccessId($p) { $this->setField('wopi_access_id', $p); }
	public function getWopiAccessId() { return $this->getField('wopi_access_id'); }
	
		
	public function setAccessToken($p) { $this->setField('access_token', $p); }
	public function getAccessToken() { return $this->getField('access_token'); }
	
		
	public function setAccessTokenTtl($p) { $this->setField('access_token_ttl', $p); }
	public function getAccessTokenTtl() { return $this->getField('access_token_ttl'); }
	
		
	public function setPath($p) { $this->setField('path', $p); }
	public function getPath() { return $this->getField('path'); }
	
		
	public function setUserId($p) { $this->setField('user_id', $p); }
	public function getUserId() { return $this->getField('user_id'); }
	
		
	public function setEdited($p) { $this->setField('edited', $p); }
	public function getEdited() { return $this->getField('edited'); }
	
		
	public function setCreated($p) { $this->setField('created', $p); }
	public function getCreated() { return $this->getField('created'); }
	
	
}

