<?php


namespace project\model\base;


class ProjectBase extends \core\db\DBObject {

	public function __construct($id=null) {
		$this->setResource( 'default' );
		$this->setTableName( 'project__project' );
		$this->setPrimaryKey( 'project_id' );
		$this->setDatabaseFields( array (
  'project_id' => 
  array (
    'Field' => 'project_id',
    'Type' => 'int(11)',
    'Null' => 'NO',
    'Key' => 'PRI',
    'Default' => NULL,
    'Extra' => 'auto_increment',
  ),
  'company_id' => 
  array (
    'Field' => 'company_id',
    'Type' => 'int(11)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
  ),
  'person_id' => 
  array (
    'Field' => 'person_id',
    'Type' => 'int(11)',
    'Null' => 'YES',
    'Key' => 'MUL',
    'Default' => NULL,
    'Extra' => '',
  ),
  'project_name' => 
  array (
    'Field' => 'project_name',
    'Type' => 'varchar(255)',
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
  'note' => 
  array (
    'Field' => 'note',
    'Type' => 'longtext',
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
	
		
	public function setProjectId($p) { $this->setField('project_id', $p); }
	public function getProjectId() { return $this->getField('project_id'); }
	
		
	public function setCompanyId($p) { $this->setField('company_id', $p); }
	public function getCompanyId() { return $this->getField('company_id'); }
	
		
	public function setPersonId($p) { $this->setField('person_id', $p); }
	public function getPersonId() { return $this->getField('person_id'); }
	
		
	public function setProjectName($p) { $this->setField('project_name', $p); }
	public function getProjectName() { return $this->getField('project_name'); }
	
		
	public function setActive($p) { $this->setField('active', $p); }
	public function getActive() { return $this->getField('active'); }
	
		
	public function setNote($p) { $this->setField('note', $p); }
	public function getNote() { return $this->getField('note'); }
	
		
	public function setEdited($p) { $this->setField('edited', $p); }
	public function getEdited() { return $this->getField('edited'); }
	
		
	public function setCreated($p) { $this->setField('created', $p); }
	public function getCreated() { return $this->getField('created'); }
	
	
}

