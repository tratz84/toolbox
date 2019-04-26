<?php


namespace base\model\base;


class ObjectMetaBase extends \core\db\DBObject {

	public function __construct($id=null) {
		$this->setResource( 'default' );
		$this->setTableName( 'base__object_meta' );
		$this->setPrimaryKey( 'object_meta_id' );
		$this->setDatabaseFields( array (
  'object_meta_id' => 
  array (
    'Field' => 'object_meta_id',
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
    'Key' => 'MUL',
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
  'object_id' => 
  array (
    'Field' => 'object_id',
    'Type' => 'int(11)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
  ),
  'object_value' => 
  array (
    'Field' => 'object_value',
    'Type' => 'longtext',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
  ),
  'object_note' => 
  array (
    'Field' => 'object_note',
    'Type' => 'text',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
  ),
) );
		
		if ($id != null)
			$this->setField($this->primaryKey, $id);
	}
	
		
	public function setObjectMetaId($p) { $this->setField('object_meta_id', $p); }
	public function getObjectMetaId() { return $this->getField('object_meta_id'); }
	
		
	public function setObjectName($p) { $this->setField('object_name', $p); }
	public function getObjectName() { return $this->getField('object_name'); }
	
		
	public function setObjectKey($p) { $this->setField('object_key', $p); }
	public function getObjectKey() { return $this->getField('object_key'); }
	
		
	public function setObjectId($p) { $this->setField('object_id', $p); }
	public function getObjectId() { return $this->getField('object_id'); }
	
		
	public function setObjectValue($p) { $this->setField('object_value', $p); }
	public function getObjectValue() { return $this->getField('object_value'); }
	
		
	public function setObjectNote($p) { $this->setField('object_note', $p); }
	public function getObjectNote() { return $this->getField('object_note'); }
	
	
}

