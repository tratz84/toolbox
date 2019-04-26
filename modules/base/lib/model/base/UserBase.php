<?php


namespace base\model\base;


class UserBase extends \core\db\DBObject {

	public function __construct($id=null) {
		$this->setResource( 'default' );
		$this->setTableName( 'base__user' );
		$this->setPrimaryKey( 'user_id' );
		$this->setDatabaseFields( array (
  'user_id' => 
  array (
    'Field' => 'user_id',
    'Type' => 'int(11)',
    'Null' => 'NO',
    'Key' => 'PRI',
    'Default' => NULL,
    'Extra' => 'auto_increment',
  ),
  'username' => 
  array (
    'Field' => 'username',
    'Type' => 'varchar(128)',
    'Null' => 'YES',
    'Key' => 'UNI',
    'Default' => NULL,
    'Extra' => '',
  ),
  'email' => 
  array (
    'Field' => 'email',
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
  'user_type' => 
  array (
    'Field' => 'user_type',
    'Type' => 'varchar(20)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
  ),
  'firstname' => 
  array (
    'Field' => 'firstname',
    'Type' => 'varchar(128)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
  ),
  'lastname' => 
  array (
    'Field' => 'lastname',
    'Type' => 'varchar(128)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
  ),
  'autologin_token' => 
  array (
    'Field' => 'autologin_token',
    'Type' => 'varchar(255)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
  ),
  'activated' => 
  array (
    'Field' => 'activated',
    'Type' => 'tinyint(1)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => '1',
    'Extra' => '',
  ),
) );
		
		if ($id != null)
			$this->setField($this->primaryKey, $id);
	}
	
		
	public function setUserId($p) { $this->setField('user_id', $p); }
	public function getUserId() { return $this->getField('user_id'); }
	
		
	public function setUsername($p) { $this->setField('username', $p); }
	public function getUsername() { return $this->getField('username'); }
	
		
	public function setEmail($p) { $this->setField('email', $p); }
	public function getEmail() { return $this->getField('email'); }
	
		
	public function setPassword($p) { $this->setField('password', $p); }
	public function getPassword() { return $this->getField('password'); }
	
		
	public function setEdited($p) { $this->setField('edited', $p); }
	public function getEdited() { return $this->getField('edited'); }
	
		
	public function setCreated($p) { $this->setField('created', $p); }
	public function getCreated() { return $this->getField('created'); }
	
		
	public function setUserType($p) { $this->setField('user_type', $p); }
	public function getUserType() { return $this->getField('user_type'); }
	
		
	public function setFirstname($p) { $this->setField('firstname', $p); }
	public function getFirstname() { return $this->getField('firstname'); }
	
		
	public function setLastname($p) { $this->setField('lastname', $p); }
	public function getLastname() { return $this->getField('lastname'); }
	
		
	public function setAutologinToken($p) { $this->setField('autologin_token', $p); }
	public function getAutologinToken() { return $this->getField('autologin_token'); }
	
		
	public function setActivated($p) { $this->setField('activated', $p); }
	public function getActivated() { return $this->getField('activated'); }
	
	
}

