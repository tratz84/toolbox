<?php


namespace base\model\base;


class PersonPhoneBase extends \core\db\DBObject {

	public function __construct($id=null) {
		$this->setResource( 'default' );
		$this->setTableName( 'customer__person_phone' );
		$this->setPrimaryKey( 'person_phone_id' );
		$this->setDatabaseFields( array (
  'person_phone_id' => 
  array (
    'Field' => 'person_phone_id',
    'Type' => 'int(11)',
    'Null' => 'NO',
    'Key' => 'PRI',
    'Default' => NULL,
    'Extra' => 'auto_increment',
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
  'phone_id' => 
  array (
    'Field' => 'phone_id',
    'Type' => 'int(11)',
    'Null' => 'YES',
    'Key' => 'MUL',
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
) );
		
		if ($id != null)
			$this->setField($this->primaryKey, $id);
	}
	
		
	public function setPersonPhoneId($p) { $this->setField('person_phone_id', $p); }
	public function getPersonPhoneId() { return $this->getField('person_phone_id'); }
	
		
	public function setPersonId($p) { $this->setField('person_id', $p); }
	public function getPersonId() { return $this->getField('person_id'); }
	
		
	public function setPhoneId($p) { $this->setField('phone_id', $p); }
	public function getPhoneId() { return $this->getField('phone_id'); }
	
		
	public function setSort($p) { $this->setField('sort', $p); }
	public function getSort() { return $this->getField('sort'); }
	
	
}

