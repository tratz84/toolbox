<?php


namespace base\model\base;


class FileBase extends \core\db\DBObject {

	public function __construct($id=null) {
		$this->setResource( 'default' );
		$this->setTableName( 'base__file' );
		$this->setPrimaryKey( 'file_id' );
		$this->setDatabaseFields( array (
  'file_id' => 
  array (
    'Field' => 'file_id',
    'Type' => 'int(11)',
    'Null' => 'NO',
    'Key' => 'PRI',
    'Default' => NULL,
    'Extra' => 'auto_increment',
  ),
  'ref_count' => 
  array (
    'Field' => 'ref_count',
    'Type' => 'int(11)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => '0',
    'Extra' => '',
  ),
  'filename' => 
  array (
    'Field' => 'filename',
    'Type' => 'varchar(255)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
  ),
  'filesize' => 
  array (
    'Field' => 'filesize',
    'Type' => 'int(11)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
  ),
  'module_name' => 
  array (
    'Field' => 'module_name',
    'Type' => 'varchar(128)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
  ),
  'category_name' => 
  array (
    'Field' => 'category_name',
    'Type' => 'varchar(128)',
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
	
		
	public function setFileId($p) { $this->setField('file_id', $p); }
	public function getFileId() { return $this->getField('file_id'); }
	
		
	public function setRefCount($p) { $this->setField('ref_count', $p); }
	public function getRefCount() { return $this->getField('ref_count'); }
	
		
	public function setFilename($p) { $this->setField('filename', $p); }
	public function getFilename() { return $this->getField('filename'); }
	
		
	public function setFilesize($p) { $this->setField('filesize', $p); }
	public function getFilesize() { return $this->getField('filesize'); }
	
		
	public function setModuleName($p) { $this->setField('module_name', $p); }
	public function getModuleName() { return $this->getField('module_name'); }
	
		
	public function setCategoryName($p) { $this->setField('category_name', $p); }
	public function getCategoryName() { return $this->getField('category_name'); }
	
		
	public function setEdited($p) { $this->setField('edited', $p); }
	public function getEdited() { return $this->getField('edited'); }
	
		
	public function setCreated($p) { $this->setField('created', $p); }
	public function getCreated() { return $this->getField('created'); }
	
	
}

