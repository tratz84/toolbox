<?php


namespace base\model\base;


class UserGroupCapabilityBase extends \core\db\DBObject {

	public function __construct($id=null) {
		$this->setResource( 'default' );
		$this->setTableName( 'base__user_group_capability' );
		$this->setPrimaryKey( 'user_group_capability_id' );
		$this->setDatabaseFields( array (
  'user_group_capability_id' => 
  array (
    'Field' => 'user_group_capability_id',
    'Type' => 'int',
    'Null' => 'NO',
    'Key' => 'PRI',
    'Default' => NULL,
    'Extra' => 'auto_increment',
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
  'user_group_id' => 
  array (
    'Field' => 'user_group_id',
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
	
		
	public function setUserGroupCapabilityId($p) { $this->setField('user_group_capability_id', $p); }
	public function getUserGroupCapabilityId() { return $this->getField('user_group_capability_id'); }
	
		
	public function setModuleName($p) { $this->setField('module_name', $p); }
	public function getModuleName() { return $this->getField('module_name'); }
	
		
	public function setCapabilityCode($p) { $this->setField('capability_code', $p); }
	public function getCapabilityCode() { return $this->getField('capability_code'); }
	
		
	public function setCreated($p) { $this->setField('created', $p); }
	public function getCreated() { return $this->getField('created'); }
	
		
	public function setUserGroupId($p) { $this->setField('user_group_id', $p); }
	public function getUserGroupId() { return $this->getField('user_group_id'); }
	
	
}

