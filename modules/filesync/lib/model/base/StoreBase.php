<?php


namespace filesync\model\base;


class StoreBase extends \core\db\DBObject {

	public function __construct($id=null) {
		$this->setResource( 'default' );
		$this->setTableName( 'filesync__store' );
		$this->setPrimaryKey( 'store_id' );
		$this->setDatabaseFields( array (
  'store_id' => 
  array (
    'Field' => 'store_id',
    'Type' => 'int(11)',
    'Null' => 'NO',
    'Key' => 'PRI',
    'Default' => NULL,
    'Extra' => 'auto_increment',
  ),
  'store_type' => 
  array (
    'Field' => 'store_type',
    'Type' => 'varchar(16)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
  ),
  'store_name' => 
  array (
    'Field' => 'store_name',
    'Type' => 'varchar(128)',
    'Null' => 'YES',
    'Key' => 'UNI',
    'Default' => NULL,
    'Extra' => '',
  ),
  'note' => 
  array (
    'Field' => 'note',
    'Type' => 'mediumtext',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
  ),
  'last_file_change' => 
  array (
    'Field' => 'last_file_change',
    'Type' => 'bigint(20)',
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
) );
		
		if ($id != null)
			$this->setField($this->primaryKey, $id);
	}
	
		
	public function setStoreId($p) { $this->setField('store_id', $p); }
	public function getStoreId() { return $this->getField('store_id'); }
	
		
	public function setStoreType($p) { $this->setField('store_type', $p); }
	public function getStoreType() { return $this->getField('store_type'); }
	
		
	public function setStoreName($p) { $this->setField('store_name', $p); }
	public function getStoreName() { return $this->getField('store_name'); }
	
		
	public function setNote($p) { $this->setField('note', $p); }
	public function getNote() { return $this->getField('note'); }
	
		
	public function setLastFileChange($p) { $this->setField('last_file_change', $p); }
	public function getLastFileChange() { return $this->getField('last_file_change'); }
	
		
	public function setEdited($p) { $this->setField('edited', $p); }
	public function getEdited() { return $this->getField('edited'); }
	
		
	public function setCreated($p) { $this->setField('created', $p); }
	public function getCreated() { return $this->getField('created'); }
	
	
}

