<?php


namespace customer\model\base;


class CompanyBase extends \core\db\DBObject {

	public function __construct($id=null) {
		$this->setResource( 'default' );
		$this->setTableName( 'customer__company' );
		$this->setPrimaryKey( 'company_id' );
		$this->setDatabaseFields( array (
  'company_id' => 
  array (
    'Field' => 'company_id',
    'Type' => 'int(11)',
    'Null' => 'NO',
    'Key' => 'PRI',
    'Default' => NULL,
    'Extra' => 'auto_increment',
  ),
  'company_name' => 
  array (
    'Field' => 'company_name',
    'Type' => 'varchar(255)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
  ),
  'contact_person' => 
  array (
    'Field' => 'contact_person',
    'Type' => 'varchar(128)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
  ),
  'coc_number' => 
  array (
    'Field' => 'coc_number',
    'Type' => 'varchar(128)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
  ),
  'vat_number' => 
  array (
    'Field' => 'vat_number',
    'Type' => 'varchar(64)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
  ),
  'iban' => 
  array (
    'Field' => 'iban',
    'Type' => 'varchar(64)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
  ),
  'bic' => 
  array (
    'Field' => 'bic',
    'Type' => 'varchar(32)',
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
  'deleted' => 
  array (
    'Field' => 'deleted',
    'Type' => 'tinyint(1)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => '0',
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
  'company_type_id' => 
  array (
    'Field' => 'company_type_id',
    'Type' => 'int(11)',
    'Null' => 'YES',
    'Key' => 'MUL',
    'Default' => NULL,
    'Extra' => '',
  ),
) );
		
		if ($id != null)
			$this->setField($this->primaryKey, $id);
	}
	
		
	public function setCompanyId($p) { $this->setField('company_id', $p); }
	public function getCompanyId() { return $this->getField('company_id'); }
	
		
	public function setCompanyName($p) { $this->setField('company_name', $p); }
	public function getCompanyName() { return $this->getField('company_name'); }
	
		
	public function setContactPerson($p) { $this->setField('contact_person', $p); }
	public function getContactPerson() { return $this->getField('contact_person'); }
	
		
	public function setCocNumber($p) { $this->setField('coc_number', $p); }
	public function getCocNumber() { return $this->getField('coc_number'); }
	
		
	public function setVatNumber($p) { $this->setField('vat_number', $p); }
	public function getVatNumber() { return $this->getField('vat_number'); }
	
		
	public function setIban($p) { $this->setField('iban', $p); }
	public function getIban() { return $this->getField('iban'); }
	
		
	public function setBic($p) { $this->setField('bic', $p); }
	public function getBic() { return $this->getField('bic'); }
	
		
	public function setNote($p) { $this->setField('note', $p); }
	public function getNote() { return $this->getField('note'); }
	
		
	public function setDeleted($p) { $this->setField('deleted', $p); }
	public function getDeleted() { return $this->getField('deleted'); }
	
		
	public function setEdited($p) { $this->setField('edited', $p); }
	public function getEdited() { return $this->getField('edited'); }
	
		
	public function setCreated($p) { $this->setField('created', $p); }
	public function getCreated() { return $this->getField('created'); }
	
		
	public function setCompanyTypeId($p) { $this->setField('company_type_id', $p); }
	public function getCompanyTypeId() { return $this->getField('company_type_id'); }
	
	
}

