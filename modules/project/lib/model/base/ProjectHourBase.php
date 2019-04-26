<?php


namespace project\model\base;


class ProjectHourBase extends \core\db\DBObject {

	public function __construct($id=null) {
		$this->setResource( 'default' );
		$this->setTableName( 'project__project_hour' );
		$this->setPrimaryKey( 'project_hour_id' );
		$this->setDatabaseFields( array (
  'project_hour_id' => 
  array (
    'Field' => 'project_hour_id',
    'Type' => 'int(11)',
    'Null' => 'NO',
    'Key' => 'PRI',
    'Default' => NULL,
    'Extra' => 'auto_increment',
  ),
  'project_id' => 
  array (
    'Field' => 'project_id',
    'Type' => 'int(11)',
    'Null' => 'YES',
    'Key' => 'MUL',
    'Default' => NULL,
    'Extra' => '',
  ),
  'project_hour_type_id' => 
  array (
    'Field' => 'project_hour_type_id',
    'Type' => 'int(11)',
    'Null' => 'YES',
    'Key' => 'MUL',
    'Default' => NULL,
    'Extra' => '',
  ),
  'project_hour_status_id' => 
  array (
    'Field' => 'project_hour_status_id',
    'Type' => 'int(11)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
  ),
  'registration_type' => 
  array (
    'Field' => 'registration_type',
    'Type' => 'enum(\'from_to\',\'duration\')',
    'Null' => 'YES',
    'Key' => '',
    'Default' => 'from_to',
    'Extra' => '',
  ),
  'short_description' => 
  array (
    'Field' => 'short_description',
    'Type' => 'longtext',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
  ),
  'long_description' => 
  array (
    'Field' => 'long_description',
    'Type' => 'longtext',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
  ),
  'start_time' => 
  array (
    'Field' => 'start_time',
    'Type' => 'datetime',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
  ),
  'end_time' => 
  array (
    'Field' => 'end_time',
    'Type' => 'datetime',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
  ),
  'duration' => 
  array (
    'Field' => 'duration',
    'Type' => 'double',
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
  'user_id' => 
  array (
    'Field' => 'user_id',
    'Type' => 'int(11)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
  ),
  'declarable' => 
  array (
    'Field' => 'declarable',
    'Type' => 'tinyint(1)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => '1',
    'Extra' => '',
  ),
) );
		
		if ($id != null)
			$this->setField($this->primaryKey, $id);
	}
	
		
	public function setProjectHourId($p) { $this->setField('project_hour_id', $p); }
	public function getProjectHourId() { return $this->getField('project_hour_id'); }
	
		
	public function setProjectId($p) { $this->setField('project_id', $p); }
	public function getProjectId() { return $this->getField('project_id'); }
	
		
	public function setProjectHourTypeId($p) { $this->setField('project_hour_type_id', $p); }
	public function getProjectHourTypeId() { return $this->getField('project_hour_type_id'); }
	
		
	public function setProjectHourStatusId($p) { $this->setField('project_hour_status_id', $p); }
	public function getProjectHourStatusId() { return $this->getField('project_hour_status_id'); }
	
		
	public function setRegistrationType($p) { $this->setField('registration_type', $p); }
	public function getRegistrationType() { return $this->getField('registration_type'); }
	
		
	public function setShortDescription($p) { $this->setField('short_description', $p); }
	public function getShortDescription() { return $this->getField('short_description'); }
	
		
	public function setLongDescription($p) { $this->setField('long_description', $p); }
	public function getLongDescription() { return $this->getField('long_description'); }
	
		
	public function setStartTime($p) { $this->setField('start_time', $p); }
	public function getStartTime() { return $this->getField('start_time'); }
	
		
	public function setEndTime($p) { $this->setField('end_time', $p); }
	public function getEndTime() { return $this->getField('end_time'); }
	
		
	public function setDuration($p) { $this->setField('duration', $p); }
	public function getDuration() { return $this->getField('duration'); }
	
		
	public function setEdited($p) { $this->setField('edited', $p); }
	public function getEdited() { return $this->getField('edited'); }
	
		
	public function setCreated($p) { $this->setField('created', $p); }
	public function getCreated() { return $this->getField('created'); }
	
		
	public function setUserId($p) { $this->setField('user_id', $p); }
	public function getUserId() { return $this->getField('user_id'); }
	
		
	public function setDeclarable($p) { $this->setField('declarable', $p); }
	public function getDeclarable() { return $this->getField('declarable'); }
	
	
}

