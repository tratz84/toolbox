<?php


namespace fastsite\model\base;


class WebformBase extends \core\db\DBObject {

	public function __construct($id=null) {
		$this->setResource( 'default' );
		$this->setTableName( 'fastsite__webform' );
		$this->setPrimaryKey( 'webform_id' );
		$this->setDatabaseFields( array (
  'webform_id' => 
  array (
    'Field' => 'webform_id',
    'Type' => 'int(11)',
    'Null' => 'NO',
    'Key' => 'PRI',
    'Default' => NULL,
    'Extra' => 'auto_increment',
  ),
  'webform_name' => 
  array (
    'Field' => 'webform_name',
    'Type' => 'varchar(255)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
  ),
  'webform_code' => 
  array (
    'Field' => 'webform_code',
    'Type' => 'varchar(32)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
  ),
  'confirmation_message' => 
  array (
    'Field' => 'confirmation_message',
    'Type' => 'text',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
  ),
  'active' => 
  array (
    'Field' => 'active',
    'Type' => 'tinyint(1)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => '1',
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
	
		
	public function setWebformId($p) { $this->setField('webform_id', $p); }
	public function getWebformId() { return $this->getField('webform_id'); }
	
		
	public function setWebformName($p) { $this->setField('webform_name', $p); }
	public function getWebformName() { return $this->getField('webform_name'); }
	
		
	public function setWebformCode($p) { $this->setField('webform_code', $p); }
	public function getWebformCode() { return $this->getField('webform_code'); }
	
		
	public function setConfirmationMessage($p) { $this->setField('confirmation_message', $p); }
	public function getConfirmationMessage() { return $this->getField('confirmation_message'); }
	
		
	public function setActive($p) { $this->setField('active', $p); }
	public function getActive() { return $this->getField('active'); }
	
		
	public function setEdited($p) { $this->setField('edited', $p); }
	public function getEdited() { return $this->getField('edited'); }
	
		
	public function setCreated($p) { $this->setField('created', $p); }
	public function getCreated() { return $this->getField('created'); }
	
	
}

