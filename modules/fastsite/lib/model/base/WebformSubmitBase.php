<?php


namespace fastsite\model\base;


class WebformSubmitBase extends \core\db\DBObject {

	public function __construct($id=null) {
		$this->setResource( 'default' );
		$this->setTableName( 'fastsite__webform_submit' );
		$this->setPrimaryKey( 'webform_submit_id' );
		$this->setDatabaseFields( array (
  'webform_submit_id' => 
  array (
    'Field' => 'webform_submit_id',
    'Type' => 'int(11)',
    'Null' => 'NO',
    'Key' => 'PRI',
    'Default' => NULL,
    'Extra' => 'auto_increment',
  ),
  'webform_id' => 
  array (
    'Field' => 'webform_id',
    'Type' => 'int(11)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
  ),
  'raw_request' => 
  array (
    'Field' => 'raw_request',
    'Type' => 'text',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
  ),
  'raw_server' => 
  array (
    'Field' => 'raw_server',
    'Type' => 'text',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
  ),
  'field_list' => 
  array (
    'Field' => 'field_list',
    'Type' => 'text',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
  ),
  'field_values' => 
  array (
    'Field' => 'field_values',
    'Type' => 'text',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
  ),
  'ip' => 
  array (
    'Field' => 'ip',
    'Type' => 'varchar(40)',
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
    'Default' => '0',
    'Extra' => '',
  ),
  'completed' => 
  array (
    'Field' => 'completed',
    'Type' => 'tinyint(1)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => '1',
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
	
		
	public function setWebformSubmitId($p) { $this->setField('webform_submit_id', $p); }
	public function getWebformSubmitId() { return $this->getField('webform_submit_id'); }
	
		
	public function setWebformId($p) { $this->setField('webform_id', $p); }
	public function getWebformId() { return $this->getField('webform_id'); }
	
		
	public function setRawRequest($p) { $this->setField('raw_request', $p); }
	public function getRawRequest() { return $this->getField('raw_request'); }
	
		
	public function setRawServer($p) { $this->setField('raw_server', $p); }
	public function getRawServer() { return $this->getField('raw_server'); }
	
		
	public function setFieldList($p) { $this->setField('field_list', $p); }
	public function getFieldList() { return $this->getField('field_list'); }
	
		
	public function setFieldValues($p) { $this->setField('field_values', $p); }
	public function getFieldValues() { return $this->getField('field_values'); }
	
		
	public function setIp($p) { $this->setField('ip', $p); }
	public function getIp() { return $this->getField('ip'); }
	
		
	public function setDeleted($p) { $this->setField('deleted', $p); }
	public function getDeleted() { return $this->getField('deleted'); }
	
		
	public function setCompleted($p) { $this->setField('completed', $p); }
	public function getCompleted() { return $this->getField('completed'); }
	
		
	public function setCreated($p) { $this->setField('created', $p); }
	public function getCreated() { return $this->getField('created'); }
	
	
}

