<?php


namespace admin\model\base;


class AutologinBase extends \core\db\DBObject {

	public function __construct($id=null) {
		$this->setResource( 'admin' );
		$this->setTableName( 'toolbox__autologin' );
		$this->setPrimaryKey( 'autologin_id' );
		$this->setDatabaseFields( array (
  'autologin_id' => 
  array (
    'Field' => 'autologin_id',
    'Type' => 'bigint(20)',
    'Null' => 'NO',
    'Key' => 'PRI',
    'Default' => NULL,
    'Extra' => 'auto_increment',
  ),
  'contextName' => 
  array (
    'Field' => 'contextName',
    'Type' => 'varchar(64)',
    'Null' => 'NO',
    'Key' => 'MUL',
    'Default' => NULL,
    'Extra' => '',
  ),
  'securityString' => 
  array (
    'Field' => 'securityString',
    'Type' => 'varchar(128)',
    'Null' => 'NO',
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
  'ip' => 
  array (
    'Field' => 'ip',
    'Type' => 'varchar(50)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
  ),
  'lastUsed' => 
  array (
    'Field' => 'lastUsed',
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
	
		
	public function setAutologinId($p) { $this->setField('autologin_id', $p); }
	public function getAutologinId() { return $this->getField('autologin_id'); }
	
		
	public function setContextName($p) { $this->setField('contextName', $p); }
	public function getContextName() { return $this->getField('contextName'); }
	
		
	public function setSecurityString($p) { $this->setField('securityString', $p); }
	public function getSecurityString() { return $this->getField('securityString'); }
	
		
	public function setUsername($p) { $this->setField('username', $p); }
	public function getUsername() { return $this->getField('username'); }
	
		
	public function setIp($p) { $this->setField('ip', $p); }
	public function getIp() { return $this->getField('ip'); }
	
		
	public function setLastUsed($p) { $this->setField('lastUsed', $p); }
	public function getLastUsed() { return $this->getField('lastUsed'); }
	
		
	public function setCreated($p) { $this->setField('created', $p); }
	public function getCreated() { return $this->getField('created'); }
	
	
}

