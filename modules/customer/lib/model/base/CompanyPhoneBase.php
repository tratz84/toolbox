<?php


namespace customer\model\base;


class CompanyPhoneBase extends \core\db\DBObject {

	public function __construct($id=null) {
		$this->setResource( 'default' );
		$this->setTableName( 'customer__company_phone' );
		$this->setPrimaryKey( 'company_phone_id' );
		$this->setDatabaseFields( array (
  'company_phone_id' => 
  array (
    'Field' => 'company_phone_id',
    'Type' => 'int(11)',
    'Null' => 'NO',
    'Key' => 'PRI',
    'Default' => NULL,
    'Extra' => 'auto_increment',
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
  'company_id' => 
  array (
    'Field' => 'company_id',
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
	
		
	public function setCompanyPhoneId($p) { $this->setField('company_phone_id', $p); }
	public function getCompanyPhoneId() { return $this->getField('company_phone_id'); }
	
		
	public function setPhoneId($p) { $this->setField('phone_id', $p); }
	public function getPhoneId() { return $this->getField('phone_id'); }
	
		
	public function setCompanyId($p) { $this->setField('company_id', $p); }
	public function getCompanyId() { return $this->getField('company_id'); }
	
		
	public function setSort($p) { $this->setField('sort', $p); }
	public function getSort() { return $this->getField('sort'); }
	
	
}

