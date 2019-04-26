<?php


namespace base\model\base;


class CountryBase extends \core\db\DBObject {

	public function __construct($id=null) {
		$this->setResource( 'default' );
		$this->setTableName( 'customer__country' );
		$this->setPrimaryKey( 'country_id' );
		$this->setDatabaseFields( array (
  'country_id' => 
  array (
    'Field' => 'country_id',
    'Type' => 'int(11)',
    'Null' => 'NO',
    'Key' => 'PRI',
    'Default' => NULL,
    'Extra' => 'auto_increment',
  ),
  'name' => 
  array (
    'Field' => 'name',
    'Type' => 'varchar(255)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
  ),
  'country_iso2' => 
  array (
    'Field' => 'country_iso2',
    'Type' => 'varchar(2)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
  ),
  'country_iso3' => 
  array (
    'Field' => 'country_iso3',
    'Type' => 'varchar(3)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
  ),
  'country_no' => 
  array (
    'Field' => 'country_no',
    'Type' => 'varchar(3)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
  ),
  'phone_prefix' => 
  array (
    'Field' => 'phone_prefix',
    'Type' => 'varchar(20)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
  ),
) );
		
		if ($id != null)
			$this->setField($this->primaryKey, $id);
	}
	
		
	public function setCountryId($p) { $this->setField('country_id', $p); }
	public function getCountryId() { return $this->getField('country_id'); }
	
		
	public function setName($p) { $this->setField('name', $p); }
	public function getName() { return $this->getField('name'); }
	
		
	public function setCountryIso2($p) { $this->setField('country_iso2', $p); }
	public function getCountryIso2() { return $this->getField('country_iso2'); }
	
		
	public function setCountryIso3($p) { $this->setField('country_iso3', $p); }
	public function getCountryIso3() { return $this->getField('country_iso3'); }
	
		
	public function setCountryNo($p) { $this->setField('country_no', $p); }
	public function getCountryNo() { return $this->getField('country_no'); }
	
		
	public function setPhonePrefix($p) { $this->setField('phone_prefix', $p); }
	public function getPhonePrefix() { return $this->getField('phone_prefix'); }
	
	
}

