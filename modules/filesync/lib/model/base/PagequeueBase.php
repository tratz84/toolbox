<?php


namespace filesync\model\base;


class PagequeueBase extends \core\db\DBObject {

	public function __construct($id=null) {
		$this->setResource( 'default' );
		$this->setTableName( 'filesync__pagequeue' );
		$this->setPrimaryKey( 'pagequeue_id' );
		$this->setDatabaseFields( array (
  'pagequeue_id' => 
  array (
    'Field' => 'pagequeue_id',
    'Type' => 'int',
    'Null' => 'NO',
    'Key' => 'PRI',
    'Default' => NULL,
    'Extra' => 'auto_increment',
  ),
  'ref_id' => 
  array (
    'Field' => 'ref_id',
    'Type' => 'int',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
  ),
  'ref_object' => 
  array (
    'Field' => 'ref_object',
    'Type' => 'varchar(255)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
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
  'name' => 
  array (
    'Field' => 'name',
    'Type' => 'varchar(255)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
  ),
  'description' => 
  array (
    'Field' => 'description',
    'Type' => 'text',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
  ),
  'crop_x1' => 
  array (
    'Field' => 'crop_x1',
    'Type' => 'double',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
  ),
  'crop_y1' => 
  array (
    'Field' => 'crop_y1',
    'Type' => 'double',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
  ),
  'crop_x2' => 
  array (
    'Field' => 'crop_x2',
    'Type' => 'double',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
  ),
  'crop_y2' => 
  array (
    'Field' => 'crop_y2',
    'Type' => 'double',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
  ),
  'degrees_rotated' => 
  array (
    'Field' => 'degrees_rotated',
    'Type' => 'int',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
  ),
  'page_orientation' => 
  array (
    'Field' => 'page_orientation',
    'Type' => 'enum(\'P\',\'L\')',
    'Null' => 'YES',
    'Key' => '',
    'Default' => 'P',
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
  'user_id' => 
  array (
    'Field' => 'user_id',
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
	
		
	public function setPagequeueId($p) { $this->setField('pagequeue_id', $p); }
	public function getPagequeueId() { return $this->getField('pagequeue_id'); }
	
		
	public function setRefId($p) { $this->setField('ref_id', $p); }
	public function getRefId() { return $this->getField('ref_id'); }
	
		
	public function setRefObject($p) { $this->setField('ref_object', $p); }
	public function getRefObject() { return $this->getField('ref_object'); }
	
		
	public function setFilename($p) { $this->setField('filename', $p); }
	public function getFilename() { return $this->getField('filename'); }
	
		
	public function setName($p) { $this->setField('name', $p); }
	public function getName() { return $this->getField('name'); }
	
		
	public function setDescription($p) { $this->setField('description', $p); }
	public function getDescription() { return $this->getField('description'); }
	
		
	public function setCropX1($p) { $this->setField('crop_x1', $p); }
	public function getCropX1() { return $this->getField('crop_x1'); }
	
		
	public function setCropY1($p) { $this->setField('crop_y1', $p); }
	public function getCropY1() { return $this->getField('crop_y1'); }
	
		
	public function setCropX2($p) { $this->setField('crop_x2', $p); }
	public function getCropX2() { return $this->getField('crop_x2'); }
	
		
	public function setCropY2($p) { $this->setField('crop_y2', $p); }
	public function getCropY2() { return $this->getField('crop_y2'); }
	
		
	public function setDegreesRotated($p) { $this->setField('degrees_rotated', $p); }
	public function getDegreesRotated() { return $this->getField('degrees_rotated'); }
	
		
	public function setPageOrientation($p) { $this->setField('page_orientation', $p); }
	public function getPageOrientation() { return $this->getField('page_orientation'); }
	
		
	public function setEdited($p) { $this->setField('edited', $p); }
	public function getEdited() { return $this->getField('edited'); }
	
		
	public function setCreated($p) { $this->setField('created', $p); }
	public function getCreated() { return $this->getField('created'); }
	
		
	public function setUserId($p) { $this->setField('user_id', $p); }
	public function getUserId() { return $this->getField('user_id'); }
	
	
}

