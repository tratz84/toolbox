<?php


namespace filesync\model\base;


class StoreFileBase extends \core\db\DBObject {

	public function __construct($id=null) {
		$this->setResource( 'default' );
		$this->setTableName( 'filesync__store_file' );
		$this->setPrimaryKey( 'store_file_id' );
		$this->setDatabaseFields( array (
  'store_file_id' => 
  array (
    'Field' => 'store_file_id',
    'Type' => 'bigint(20)',
    'Null' => 'NO',
    'Key' => 'PRI',
    'Default' => NULL,
    'Extra' => 'auto_increment',
  ),
  'store_id' => 
  array (
    'Field' => 'store_id',
    'Type' => 'int(11)',
    'Null' => 'YES',
    'Key' => 'MUL',
    'Default' => NULL,
    'Extra' => '',
  ),
  'path' => 
  array (
    'Field' => 'path',
    'Type' => 'varchar(255)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
  ),
  'rev' => 
  array (
    'Field' => 'rev',
    'Type' => 'int(11)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
  ),
  'deleted' => 
  array (
    'Field' => 'deleted',
    'Type' => 'tinyint(1)',
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
	
		
	public function setStoreFileId($p) { $this->setField('store_file_id', $p); }
	public function getStoreFileId() { return $this->getField('store_file_id'); }
	
		
	public function setStoreId($p) { $this->setField('store_id', $p); }
	public function getStoreId() { return $this->getField('store_id'); }
	
		
	public function setPath($p) { $this->setField('path', $p); }
	public function getPath() { return $this->getField('path'); }
	
		
	public function setRev($p) { $this->setField('rev', $p); }
	public function getRev() { return $this->getField('rev'); }
	
		
	public function setDeleted($p) { $this->setField('deleted', $p); }
	public function getDeleted() { return $this->getField('deleted'); }
	
		
	public function setEdited($p) { $this->setField('edited', $p); }
	public function getEdited() { return $this->getField('edited'); }
	
		
	public function setCreated($p) { $this->setField('created', $p); }
	public function getCreated() { return $this->getField('created'); }
	
	
}

