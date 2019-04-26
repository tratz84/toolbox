<?php


namespace base\model\base;


class AddressBase extends \core\db\DBObject {

	public function __construct($id=null) {
		$this->setResource( 'default' );
		$this->setTableName( 'customer__address' );
		$this->setPrimaryKey( 'address_id' );
		$this->setDatabaseFields( array (
  'address_id' => 
  array (
    'Field' => 'address_id',
    'Type' => 'int(11)',
    'Null' => 'NO',
    'Key' => 'PRI',
    'Default' => NULL,
    'Extra' => 'auto_increment',
  ),
  'street' => 
  array (
    'Field' => 'street',
    'Type' => 'varchar(255)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
  ),
  'street_no' => 
  array (
    'Field' => 'street_no',
    'Type' => 'varchar(64)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
  ),
  'zipcode' => 
  array (
    'Field' => 'zipcode',
    'Type' => 'varchar(64)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
  ),
  'city' => 
  array (
    'Field' => 'city',
    'Type' => 'varchar(64)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
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
  'sort' => 
  array (
    'Field' => 'sort',
    'Type' => 'int(11)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
  ),
  'country_id' => 
  array (
    'Field' => 'country_id',
    'Type' => 'int(11)',
    'Null' => 'YES',
    'Key' => 'MUL',
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
	
		
	public function setAddressId($p) { $this->setField('address_id', $p); }
	public function getAddressId() { return $this->getField('address_id'); }
	
		
	public function setStreet($p) { $this->setField('street', $p); }
	public function getStreet() { return $this->getField('street'); }
	
		
	public function setStreetNo($p) { $this->setField('street_no', $p); }
	public function getStreetNo() { return $this->getField('street_no'); }
	
		
	public function setZipcode($p) { $this->setField('zipcode', $p); }
	public function getZipcode() { return $this->getField('zipcode'); }
	
		
	public function setCity($p) { $this->setField('city', $p); }
	public function getCity() { return $this->getField('city'); }
	
		
	public function setNote($p) { $this->setField('note', $p); }
	public function getNote() { return $this->getField('note'); }
	
		
	public function setSort($p) { $this->setField('sort', $p); }
	public function getSort() { return $this->getField('sort'); }
	
		
	public function setCountryId($p) { $this->setField('country_id', $p); }
	public function getCountryId() { return $this->getField('country_id'); }
	
		
	public function setEdited($p) { $this->setField('edited', $p); }
	public function getEdited() { return $this->getField('edited'); }
	
		
	public function setCreated($p) { $this->setField('created', $p); }
	public function getCreated() { return $this->getField('created'); }
	
	
}

