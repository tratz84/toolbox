<?php


namespace base\model\base;


class LockBase extends \core\db\DBObject {

	public function __construct($id=null) {
		$this->setResource( 'default' );
		$this->setTableName( 'base__lock' );
		$this->setPrimaryKey( 'lock_id' );
		$this->setDatabaseFields( array (
  'lock_id' => 
  array (
    'Field' => 'lock_id',
    'Type' => 'int',
    'Null' => 'NO',
    'Key' => 'PRI',
    'Default' => NULL,
    'Extra' => 'auto_increment',
  ),
  'lock_name' => 
  array (
    'Field' => 'lock_name',
    'Type' => 'varchar(128)',
    'Null' => 'YES',
    'Key' => 'UNI',
    'Default' => NULL,
    'Extra' => '',
  ),
  'expires' => 
  array (
    'Field' => 'expires',
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
	
		
	public function setLockId($p) { $this->setField('lock_id', $p); }
	public function getLockId() { return $this->getField('lock_id'); }
	
		
	public function setLockName($p) { $this->setField('lock_name', $p); }
	public function getLockName() { return $this->getField('lock_name'); }
	
		
	public function setExpires($p) { $this->setField('expires', $p); }
	public function getExpires() { return $this->getField('expires'); }
	
		
	public function setCreated($p) { $this->setField('created', $p); }
	public function getCreated() { return $this->getField('created'); }
	
	
}

