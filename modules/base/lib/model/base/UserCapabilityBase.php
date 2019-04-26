<?php


namespace base\model\base;


class UserCapabilityBase extends \core\db\DBObject {

	public function __construct($id=null) {
		$this->setResource( 'default' );
		$this->setTableName( 'base__user_capability' );
		$this->setPrimaryKey( 'user_capability_id' );
		$this->setDatabaseFields( array (
  'user_capability_id' => 
  array (
    'Field' => 'user_capability_id',
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
  'module_name' => 
  array (
    'Field' => 'module_name',
    'Type' => 'varchar(32)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
  ),
  'capability_code' => 
  array (
    'Field' => 'capability_code',
    'Type' => 'varchar(64)',
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
	
		
	public function setUserCapabilityId($p) { $this->setField('user_capability_id', $p); }
	public function getUserCapabilityId() { return $this->getField('user_capability_id'); }
	
		
	public function setUserId($p) { $this->setField('user_id', $p); }
	public function getUserId() { return $this->getField('user_id'); }
	
		
	public function setModuleName($p) { $this->setField('module_name', $p); }
	public function getModuleName() { return $this->getField('module_name'); }
	
		
	public function setCapabilityCode($p) { $this->setField('capability_code', $p); }
	public function getCapabilityCode() { return $this->getField('capability_code'); }
	
		
	public function setCreated($p) { $this->setField('created', $p); }
	public function getCreated() { return $this->getField('created'); }
	
	
}

