<?php


namespace filesync\model\base;


class StoreFileRevBase extends \core\db\DBObject {

	public function __construct($id=null) {
		$this->setResource( 'default' );
		$this->setTableName( 'filesync__store_file_rev' );
		$this->setPrimaryKey( 'store_file_rev_id' );
		$this->setDatabaseFields( array (
  'store_file_rev_id' => 
  array (
    'Field' => 'store_file_rev_id',
    'Type' => 'bigint(20)',
    'Null' => 'NO',
    'Key' => 'PRI',
    'Default' => NULL,
    'Extra' => 'auto_increment',
  ),
  'store_file_id' => 
  array (
    'Field' => 'store_file_id',
    'Type' => 'bigint(20)',
    'Null' => 'YES',
    'Key' => 'MUL',
    'Default' => NULL,
    'Extra' => '',
  ),
  'filesize' => 
  array (
    'Field' => 'filesize',
    'Type' => 'bigint(20)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
  ),
  'md5sum' => 
  array (
    'Field' => 'md5sum',
    'Type' => 'varchar(32)',
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
  'lastmodified' => 
  array (
    'Field' => 'lastmodified',
    'Type' => 'datetime',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
  ),
  'encrypted' => 
  array (
    'Field' => 'encrypted',
    'Type' => 'tinyint(1)',
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
	
		
	public function setStoreFileRevId($p) { $this->setField('store_file_rev_id', $p); }
	public function getStoreFileRevId() { return $this->getField('store_file_rev_id'); }
	
		
	public function setStoreFileId($p) { $this->setField('store_file_id', $p); }
	public function getStoreFileId() { return $this->getField('store_file_id'); }
	
		
	public function setFilesize($p) { $this->setField('filesize', $p); }
	public function getFilesize() { return $this->getField('filesize'); }
	
		
	public function setMd5sum($p) { $this->setField('md5sum', $p); }
	public function getMd5sum() { return $this->getField('md5sum'); }
	
		
	public function setRev($p) { $this->setField('rev', $p); }
	public function getRev() { return $this->getField('rev'); }
	
		
	public function setLastmodified($p) { $this->setField('lastmodified', $p); }
	public function getLastmodified() { return $this->getField('lastmodified'); }
	
		
	public function setEncrypted($p) { $this->setField('encrypted', $p); }
	public function getEncrypted() { return $this->getField('encrypted'); }
	
		
	public function setCreated($p) { $this->setField('created', $p); }
	public function getCreated() { return $this->getField('created'); }
	
	
}

