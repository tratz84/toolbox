<?php


namespace webmail\model\base;


class EmailStatusBase extends \core\db\DBObject {

	public function __construct($id=null) {
		$this->setResource( 'default' );
		$this->setTableName( 'webmail__email_status' );
		$this->setPrimaryKey( 'email_status_id' );
		$this->setDatabaseFields( array (
  'email_status_id' => 
  array (
    'Field' => 'email_status_id',
    'Type' => 'int(11)',
    'Null' => 'NO',
    'Key' => 'PRI',
    'Default' => NULL,
    'Extra' => 'auto_increment',
  ),
  'status_name' => 
  array (
    'Field' => 'status_name',
    'Type' => 'varchar(255)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
  ),
  'default_selected' => 
  array (
    'Field' => 'default_selected',
    'Type' => 'tinyint(1)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
  ),
  'sort' => 
  array (
    'Field' => 'sort',
    'Type' => 'int(11)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
  ),
  'visible' => 
  array (
    'Field' => 'visible',
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
	
		
	public function setEmailStatusId($p) { $this->setField('email_status_id', $p); }
	public function getEmailStatusId() { return $this->getField('email_status_id'); }
	
		
	public function setStatusName($p) { $this->setField('status_name', $p); }
	public function getStatusName() { return $this->getField('status_name'); }
	
		
	public function setDefaultSelected($p) { $this->setField('default_selected', $p); }
	public function getDefaultSelected() { return $this->getField('default_selected'); }
	
		
	public function setSort($p) { $this->setField('sort', $p); }
	public function getSort() { return $this->getField('sort'); }
	
		
	public function setVisible($p) { $this->setField('visible', $p); }
	public function getVisible() { return $this->getField('visible'); }
	
		
	public function setEdited($p) { $this->setField('edited', $p); }
	public function getEdited() { return $this->getField('edited'); }
	
		
	public function setCreated($p) { $this->setField('created', $p); }
	public function getCreated() { return $this->getField('created'); }
	
	
}

