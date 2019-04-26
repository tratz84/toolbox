<?php


namespace base\model\base;


class ActivityBase extends \core\db\DBObject {

	public function __construct($id=null) {
		$this->setResource( 'default' );
		$this->setTableName( 'base__activity' );
		$this->setPrimaryKey( 'activity_id' );
		$this->setDatabaseFields( array (
  'activity_id' => 
  array (
    'Field' => 'activity_id',
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
  'company_id' => 
  array (
    'Field' => 'company_id',
    'Type' => 'int(11)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
  ),
  'person_id' => 
  array (
    'Field' => 'person_id',
    'Type' => 'int(11)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
  ),
  'ref_object' => 
  array (
    'Field' => 'ref_object',
    'Type' => 'varchar(32)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
  ),
  'ref_id' => 
  array (
    'Field' => 'ref_id',
    'Type' => 'int(11)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
  ),
  'code' => 
  array (
    'Field' => 'code',
    'Type' => 'varchar(32)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
  ),
  'short_description' => 
  array (
    'Field' => 'short_description',
    'Type' => 'text',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
  ),
  'long_description' => 
  array (
    'Field' => 'long_description',
    'Type' => 'text',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
  ),
  'note' => 
  array (
    'Field' => 'note',
    'Type' => 'text',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
  ),
  'changes' => 
  array (
    'Field' => 'changes',
    'Type' => 'text',
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
	
		
	public function setActivityId($p) { $this->setField('activity_id', $p); }
	public function getActivityId() { return $this->getField('activity_id'); }
	
		
	public function setUserId($p) { $this->setField('user_id', $p); }
	public function getUserId() { return $this->getField('user_id'); }
	
		
	public function setUsername($p) { $this->setField('username', $p); }
	public function getUsername() { return $this->getField('username'); }
	
		
	public function setCompanyId($p) { $this->setField('company_id', $p); }
	public function getCompanyId() { return $this->getField('company_id'); }
	
		
	public function setPersonId($p) { $this->setField('person_id', $p); }
	public function getPersonId() { return $this->getField('person_id'); }
	
		
	public function setRefObject($p) { $this->setField('ref_object', $p); }
	public function getRefObject() { return $this->getField('ref_object'); }
	
		
	public function setRefId($p) { $this->setField('ref_id', $p); }
	public function getRefId() { return $this->getField('ref_id'); }
	
		
	public function setCode($p) { $this->setField('code', $p); }
	public function getCode() { return $this->getField('code'); }
	
		
	public function setShortDescription($p) { $this->setField('short_description', $p); }
	public function getShortDescription() { return $this->getField('short_description'); }
	
		
	public function setLongDescription($p) { $this->setField('long_description', $p); }
	public function getLongDescription() { return $this->getField('long_description'); }
	
		
	public function setNote($p) { $this->setField('note', $p); }
	public function getNote() { return $this->getField('note'); }
	
		
	public function setChanges($p) { $this->setField('changes', $p); }
	public function getChanges() { return $this->getField('changes'); }
	
		
	public function setCreated($p) { $this->setField('created', $p); }
	public function getCreated() { return $this->getField('created'); }
	
	
}

