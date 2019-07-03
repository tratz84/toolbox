<?php


namespace base\model\base;


class ObjectLogBase extends \core\db\DBObject {

	public function __construct($id=null) {
		$this->setResource( 'default' );
		$this->setTableName( 'base__object_log' );
		$this->setPrimaryKey( 'object_log_id' );
		$this->setDatabaseFields( array (
  'object_log_id' => 
  array (
    'Field' => 'object_log_id',
    'Type' => 'bigint(20)',
    'Null' => 'NO',
    'Key' => 'PRI',
    'Default' => NULL,
    'Extra' => 'auto_increment',
  ),
  'object_name' => 
  array (
    'Field' => 'object_name',
    'Type' => 'varchar(128)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
  ),
  'object_id' => 
  array (
    'Field' => 'object_id',
    'Type' => 'int(11)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
  ),
  'object_key' => 
  array (
    'Field' => 'object_key',
    'Type' => 'varchar(128)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
  ),
  'object_action' => 
  array (
    'Field' => 'object_action',
    'Type' => 'varchar(8)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
  ),
  'value_old' => 
  array (
    'Field' => 'value_old',
    'Type' => 'text',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
  ),
  'value_new' => 
  array (
    'Field' => 'value_new',
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
	
		
	public function setObjectLogId($p) { $this->setField('object_log_id', $p); }
	public function getObjectLogId() { return $this->getField('object_log_id'); }
	
		
	public function setObjectName($p) { $this->setField('object_name', $p); }
	public function getObjectName() { return $this->getField('object_name'); }
	
		
	public function setObjectId($p) { $this->setField('object_id', $p); }
	public function getObjectId() { return $this->getField('object_id'); }
	
		
	public function setObjectKey($p) { $this->setField('object_key', $p); }
	public function getObjectKey() { return $this->getField('object_key'); }
	
		
	public function setObjectAction($p) { $this->setField('object_action', $p); }
	public function getObjectAction() { return $this->getField('object_action'); }
	
		
	public function setValueOld($p) { $this->setField('value_old', $p); }
	public function getValueOld() { return $this->getField('value_old'); }
	
		
	public function setValueNew($p) { $this->setField('value_new', $p); }
	public function getValueNew() { return $this->getField('value_new'); }
	
		
	public function setCreated($p) { $this->setField('created', $p); }
	public function getCreated() { return $this->getField('created'); }
	
	
}

