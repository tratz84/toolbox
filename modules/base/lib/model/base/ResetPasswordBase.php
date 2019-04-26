<?php


namespace base\model\base;


class ResetPasswordBase extends \core\db\DBObject {

	public function __construct($id=null) {
		$this->setResource( 'default' );
		$this->setTableName( 'base__reset_password' );
		$this->setPrimaryKey( 'reset_password_id' );
		$this->setDatabaseFields( array (
  'reset_password_id' => 
  array (
    'Field' => 'reset_password_id',
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
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
  ),
  'username' => 
  array (
    'Field' => 'username',
    'Type' => 'varchar(128)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
  ),
  'security_string' => 
  array (
    'Field' => 'security_string',
    'Type' => 'varchar(128)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
  ),
  'request_ip' => 
  array (
    'Field' => 'request_ip',
    'Type' => 'varchar(64)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
  ),
  'used_ip' => 
  array (
    'Field' => 'used_ip',
    'Type' => 'varchar(64)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
  ),
  'used' => 
  array (
    'Field' => 'used',
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
	
		
	public function setResetPasswordId($p) { $this->setField('reset_password_id', $p); }
	public function getResetPasswordId() { return $this->getField('reset_password_id'); }
	
		
	public function setUserId($p) { $this->setField('user_id', $p); }
	public function getUserId() { return $this->getField('user_id'); }
	
		
	public function setUsername($p) { $this->setField('username', $p); }
	public function getUsername() { return $this->getField('username'); }
	
		
	public function setSecurityString($p) { $this->setField('security_string', $p); }
	public function getSecurityString() { return $this->getField('security_string'); }
	
		
	public function setRequestIp($p) { $this->setField('request_ip', $p); }
	public function getRequestIp() { return $this->getField('request_ip'); }
	
		
	public function setUsedIp($p) { $this->setField('used_ip', $p); }
	public function getUsedIp() { return $this->getField('used_ip'); }
	
		
	public function setUsed($p) { $this->setField('used', $p); }
	public function getUsed() { return $this->getField('used'); }
	
		
	public function setCreated($p) { $this->setField('created', $p); }
	public function getCreated() { return $this->getField('created'); }
	
	
}

