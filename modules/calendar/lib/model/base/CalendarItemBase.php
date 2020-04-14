<?php


namespace calendar\model\base;


class CalendarItemBase extends \core\db\DBObject {

	public function __construct($id=null) {
		$this->setResource( 'default' );
		$this->setTableName( 'cal__calendar_item' );
		$this->setPrimaryKey( 'calendar_item_id' );
		$this->setDatabaseFields( array (
  'calendar_item_id' => 
  array (
    'Field' => 'calendar_item_id',
    'Type' => 'int',
    'Null' => 'NO',
    'Key' => 'PRI',
    'Default' => NULL,
    'Extra' => 'auto_increment',
  ),
  'ref_calendar_item_id' => 
  array (
    'Field' => 'ref_calendar_item_id',
    'Type' => 'int',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
  ),
  'calendar_id' => 
  array (
    'Field' => 'calendar_id',
    'Type' => 'int',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
  ),
  'calendar_item_status_id' => 
  array (
    'Field' => 'calendar_item_status_id',
    'Type' => 'int',
    'Null' => 'YES',
    'Key' => 'MUL',
    'Default' => NULL,
    'Extra' => '',
  ),
  'calendar_item_category_id' => 
  array (
    'Field' => 'calendar_item_category_id',
    'Type' => 'int',
    'Null' => 'YES',
    'Key' => 'MUL',
    'Default' => NULL,
    'Extra' => '',
  ),
  'title' => 
  array (
    'Field' => 'title',
    'Type' => 'varchar(255)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
  ),
  'location' => 
  array (
    'Field' => 'location',
    'Type' => 'varchar(255)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
  ),
  'all_day' => 
  array (
    'Field' => 'all_day',
    'Type' => 'tinyint(1)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
  ),
  'private' => 
  array (
    'Field' => 'private',
    'Type' => 'tinyint(1)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
  ),
  'start_date' => 
  array (
    'Field' => 'start_date',
    'Type' => 'date',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
  ),
  'start_time' => 
  array (
    'Field' => 'start_time',
    'Type' => 'time',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
  ),
  'end_date' => 
  array (
    'Field' => 'end_date',
    'Type' => 'date',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
  ),
  'end_time' => 
  array (
    'Field' => 'end_time',
    'Type' => 'time',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
  ),
  'reminder' => 
  array (
    'Field' => 'reminder',
    'Type' => 'int',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
  ),
  'recurrence_type' => 
  array (
    'Field' => 'recurrence_type',
    'Type' => 'varchar(16)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
  ),
  'recurrence_rule' => 
  array (
    'Field' => 'recurrence_rule',
    'Type' => 'varchar(255)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
  ),
  'message' => 
  array (
    'Field' => 'message',
    'Type' => 'text',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
  ),
  'exdate' => 
  array (
    'Field' => 'exdate',
    'Type' => 'longtext',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
  ),
  'cancelled' => 
  array (
    'Field' => 'cancelled',
    'Type' => 'tinyint(1)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => '0',
    'Extra' => '',
  ),
  'deleted' => 
  array (
    'Field' => 'deleted',
    'Type' => 'datetime',
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
  'item_action' => 
  array (
    'Field' => 'item_action',
    'Type' => 'varchar(16)',
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
	
		
	public function setCalendarItemId($p) { $this->setField('calendar_item_id', $p); }
	public function getCalendarItemId() { return $this->getField('calendar_item_id'); }
	
		
	public function setRefCalendarItemId($p) { $this->setField('ref_calendar_item_id', $p); }
	public function getRefCalendarItemId() { return $this->getField('ref_calendar_item_id'); }
	
		
	public function setCalendarId($p) { $this->setField('calendar_id', $p); }
	public function getCalendarId() { return $this->getField('calendar_id'); }
	
		
	public function setCalendarItemStatusId($p) { $this->setField('calendar_item_status_id', $p); }
	public function getCalendarItemStatusId() { return $this->getField('calendar_item_status_id'); }
	
		
	public function setCalendarItemCategoryId($p) { $this->setField('calendar_item_category_id', $p); }
	public function getCalendarItemCategoryId() { return $this->getField('calendar_item_category_id'); }
	
		
	public function setTitle($p) { $this->setField('title', $p); }
	public function getTitle() { return $this->getField('title'); }
	
		
	public function setLocation($p) { $this->setField('location', $p); }
	public function getLocation() { return $this->getField('location'); }
	
		
	public function setAllDay($p) { $this->setField('all_day', $p); }
	public function getAllDay() { return $this->getField('all_day'); }
	
		
	public function setPrivate($p) { $this->setField('private', $p); }
	public function getPrivate() { return $this->getField('private'); }
	
		
	public function setStartDate($p) { $this->setField('start_date', $p); }
	public function getStartDate() { return $this->getField('start_date'); }
	
		
	public function setStartTime($p) { $this->setField('start_time', $p); }
	public function getStartTime() { return $this->getField('start_time'); }
	
		
	public function setEndDate($p) { $this->setField('end_date', $p); }
	public function getEndDate() { return $this->getField('end_date'); }
	
		
	public function setEndTime($p) { $this->setField('end_time', $p); }
	public function getEndTime() { return $this->getField('end_time'); }
	
		
	public function setReminder($p) { $this->setField('reminder', $p); }
	public function getReminder() { return $this->getField('reminder'); }
	
		
	public function setRecurrenceType($p) { $this->setField('recurrence_type', $p); }
	public function getRecurrenceType() { return $this->getField('recurrence_type'); }
	
		
	public function setRecurrenceRule($p) { $this->setField('recurrence_rule', $p); }
	public function getRecurrenceRule() { return $this->getField('recurrence_rule'); }
	
		
	public function setMessage($p) { $this->setField('message', $p); }
	public function getMessage() { return $this->getField('message'); }
	
		
	public function setExdate($p) { $this->setField('exdate', $p); }
	public function getExdate() { return $this->getField('exdate'); }
	
		
	public function setCancelled($p) { $this->setField('cancelled', $p); }
	public function getCancelled() { return $this->getField('cancelled'); }
	
		
	public function setDeleted($p) { $this->setField('deleted', $p); }
	public function getDeleted() { return $this->getField('deleted'); }
	
		
	public function setEdited($p) { $this->setField('edited', $p); }
	public function getEdited() { return $this->getField('edited'); }
	
		
	public function setCreated($p) { $this->setField('created', $p); }
	public function getCreated() { return $this->getField('created'); }
	
		
	public function setItemAction($p) { $this->setField('item_action', $p); }
	public function getItemAction() { return $this->getField('item_action'); }
	
		
	public function setCompanyId($p) { $this->setField('company_id', $p); }
	public function getCompanyId() { return $this->getField('company_id'); }
	
		
	public function setPersonId($p) { $this->setField('person_id', $p); }
	public function getPersonId() { return $this->getField('person_id'); }
	
	
}

