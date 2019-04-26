<?php


namespace calendar\model\base;


class TodoItemBase extends \core\db\DBObject {

	public function __construct($id=null) {
		$this->setResource( 'default' );
		$this->setTableName( 'cal__todo_item' );
		$this->setPrimaryKey( 'todo_item_id' );
		$this->setDatabaseFields( array (
  'todo_item_id' => 
  array (
    'Field' => 'todo_item_id',
    'Type' => 'int(11)',
    'Null' => 'NO',
    'Key' => 'PRI',
    'Default' => NULL,
    'Extra' => 'auto_increment',
  ),
  'todo_id' => 
  array (
    'Field' => 'todo_id',
    'Type' => 'int(11)',
    'Null' => 'YES',
    'Key' => 'MUL',
    'Default' => NULL,
    'Extra' => '',
  ),
  'summary' => 
  array (
    'Field' => 'summary',
    'Type' => 'varchar(512)',
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
  'start_date' => 
  array (
    'Field' => 'start_date',
    'Type' => 'datetime',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
  ),
  'end_date' => 
  array (
    'Field' => 'end_date',
    'Type' => 'datetime',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
  ),
  'priority' => 
  array (
    'Field' => 'priority',
    'Type' => 'int(11)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
  ),
  'status' => 
  array (
    'Field' => 'status',
    'Type' => 'int(11)',
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
	
		
	public function setTodoItemId($p) { $this->setField('todo_item_id', $p); }
	public function getTodoItemId() { return $this->getField('todo_item_id'); }
	
		
	public function setTodoId($p) { $this->setField('todo_id', $p); }
	public function getTodoId() { return $this->getField('todo_id'); }
	
		
	public function setSummary($p) { $this->setField('summary', $p); }
	public function getSummary() { return $this->getField('summary'); }
	
		
	public function setLongDescription($p) { $this->setField('long_description', $p); }
	public function getLongDescription() { return $this->getField('long_description'); }
	
		
	public function setStartDate($p) { $this->setField('start_date', $p); }
	public function getStartDate() { return $this->getField('start_date'); }
	
		
	public function setEndDate($p) { $this->setField('end_date', $p); }
	public function getEndDate() { return $this->getField('end_date'); }
	
		
	public function setPriority($p) { $this->setField('priority', $p); }
	public function getPriority() { return $this->getField('priority'); }
	
		
	public function setStatus($p) { $this->setField('status', $p); }
	public function getStatus() { return $this->getField('status'); }
	
		
	public function setEdited($p) { $this->setField('edited', $p); }
	public function getEdited() { return $this->getField('edited'); }
	
		
	public function setCreated($p) { $this->setField('created', $p); }
	public function getCreated() { return $this->getField('created'); }
	
	
}

