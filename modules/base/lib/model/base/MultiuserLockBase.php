<?php


namespace base\model\base;


class MultiuserLockBase extends \core\db\DBObject {

	public function __construct($id=null) {
		$this->setResource( 'default' );
		$this->setTableName( 'base__multiuser_lock' );
		$this->setPrimaryKey( '' );
		$this->setDatabaseFields( array (
  'username' => 
  array (
    'Field' => 'username',
    'Type' => 'varchar(128)',
    'Null' => 'NO',
    'Key' => 'PRI',
    'Default' => NULL,
    'Extra' => '',
  ),
  'tabuid' => 
  array (
    'Field' => 'tabuid',
    'Type' => 'varchar(48)',
    'Null' => 'NO',
    'Key' => 'PRI',
    'Default' => NULL,
    'Extra' => '',
  ),
  'lock_key' => 
  array (
    'Field' => 'lock_key',
    'Type' => 'varchar(255)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
  ),
  'ip' => 
  array (
    'Field' => 'ip',
    'Type' => 'varchar(128)',
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
	
		
	public function setUsername($p) { $this->setField('username', $p); }
	public function getUsername() { return $this->getField('username'); }
	
		
	public function setTabuid($p) { $this->setField('tabuid', $p); }
	public function getTabuid() { return $this->getField('tabuid'); }
	
		
	public function setLockKey($p) { $this->setField('lock_key', $p); }
	public function getLockKey() { return $this->getField('lock_key'); }
	
		
	public function setIp($p) { $this->setField('ip', $p); }
	public function getIp() { return $this->getField('ip'); }
	
		
	public function setCreated($p) { $this->setField('created', $p); }
	public function getCreated() { return $this->getField('created'); }
	
	
}

