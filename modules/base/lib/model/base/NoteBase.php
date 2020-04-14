<?php


namespace base\model\base;


class NoteBase extends \core\db\DBObject {

	public function __construct($id=null) {
		$this->setResource( 'default' );
		$this->setTableName( 'base__note' );
		$this->setPrimaryKey( 'note_id' );
		$this->setDatabaseFields( array (
  'note_id' => 
  array (
    'Field' => 'note_id',
    'Type' => 'int',
    'Null' => 'NO',
    'Key' => 'PRI',
    'Default' => NULL,
    'Extra' => 'auto_increment',
  ),
  'ref_object' => 
  array (
    'Field' => 'ref_object',
    'Type' => 'varchar(32)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
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
  'short_note' => 
  array (
    'Field' => 'short_note',
    'Type' => 'text',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
  ),
  'long_note' => 
  array (
    'Field' => 'long_note',
    'Type' => 'text',
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
  'important' => 
  array (
    'Field' => 'important',
    'Type' => 'tinyint(1)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
  ),
  'company_id' => 
  array (
    'Field' => 'company_id',
    'Type' => 'int',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
  ),
  'person_id' => 
  array (
    'Field' => 'person_id',
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
	
		
	public function setNoteId($p) { $this->setField('note_id', $p); }
	public function getNoteId() { return $this->getField('note_id'); }
	
		
	public function setRefObject($p) { $this->setField('ref_object', $p); }
	public function getRefObject() { return $this->getField('ref_object'); }
	
		
	public function setRefId($p) { $this->setField('ref_id', $p); }
	public function getRefId() { return $this->getField('ref_id'); }
	
		
	public function setShortNote($p) { $this->setField('short_note', $p); }
	public function getShortNote() { return $this->getField('short_note'); }
	
		
	public function setLongNote($p) { $this->setField('long_note', $p); }
	public function getLongNote() { return $this->getField('long_note'); }
	
		
	public function setEdited($p) { $this->setField('edited', $p); }
	public function getEdited() { return $this->getField('edited'); }
	
		
	public function setCreated($p) { $this->setField('created', $p); }
	public function getCreated() { return $this->getField('created'); }
	
		
	public function setImportant($p) { $this->setField('important', $p); }
	public function getImportant() { return $this->getField('important'); }
	
		
	public function setCompanyId($p) { $this->setField('company_id', $p); }
	public function getCompanyId() { return $this->getField('company_id'); }
	
		
	public function setPersonId($p) { $this->setField('person_id', $p); }
	public function getPersonId() { return $this->getField('person_id'); }
	
	
}

