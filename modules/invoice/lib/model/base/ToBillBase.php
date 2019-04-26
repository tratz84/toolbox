<?php


namespace invoice\model\base;


class ToBillBase extends \core\db\DBObject {

	public function __construct($id=null) {
		$this->setResource( 'default' );
		$this->setTableName( 'invoice__to_bill' );
		$this->setPrimaryKey( 'to_bill_id' );
		$this->setDatabaseFields( array (
  'to_bill_id' => 
  array (
    'Field' => 'to_bill_id',
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
    'Key' => 'MUL',
    'Default' => NULL,
    'Extra' => '',
  ),
  'person_id' => 
  array (
    'Field' => 'person_id',
    'Type' => 'int(11)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
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
  'user_id' => 
  array (
    'Field' => 'user_id',
    'Type' => 'int(11)',
    'Null' => 'YES',
    'Key' => 'MUL',
    'Default' => NULL,
    'Extra' => '',
  ),
  'short_description' => 
  array (
    'Field' => 'short_description',
    'Type' => 'varchar(255)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
  ),
  'long_description' => 
  array (
    'Field' => 'long_description',
    'Type' => 'text',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
  ),
  'amount' => 
  array (
    'Field' => 'amount',
    'Type' => 'double',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
  ),
  'price' => 
  array (
    'Field' => 'price',
    'Type' => 'decimal(10,2)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
  ),
  'invoice_line_id' => 
  array (
    'Field' => 'invoice_line_id',
    'Type' => 'int(11)',
    'Null' => 'YES',
    'Key' => 'MUL',
    'Default' => NULL,
    'Extra' => '',
  ),
  'billed' => 
  array (
    'Field' => 'billed',
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
) );
		
		if ($id != null)
			$this->setField($this->primaryKey, $id);
	}
	
		
	public function setToBillId($p) { $this->setField('to_bill_id', $p); }
	public function getToBillId() { return $this->getField('to_bill_id'); }
	
		
	public function setCompanyId($p) { $this->setField('company_id', $p); }
	public function getCompanyId() { return $this->getField('company_id'); }
	
		
	public function setPersonId($p) { $this->setField('person_id', $p); }
	public function getPersonId() { return $this->getField('person_id'); }
	
		
	public function setProjectId($p) { $this->setField('project_id', $p); }
	public function getProjectId() { return $this->getField('project_id'); }
	
		
	public function setUserId($p) { $this->setField('user_id', $p); }
	public function getUserId() { return $this->getField('user_id'); }
	
		
	public function setShortDescription($p) { $this->setField('short_description', $p); }
	public function getShortDescription() { return $this->getField('short_description'); }
	
		
	public function setLongDescription($p) { $this->setField('long_description', $p); }
	public function getLongDescription() { return $this->getField('long_description'); }
	
		
	public function setAmount($p) { $this->setField('amount', $p); }
	public function getAmount() { return $this->getField('amount'); }
	
		
	public function setPrice($p) { $this->setField('price', $p); }
	public function getPrice() { return $this->getField('price'); }
	
		
	public function setInvoiceLineId($p) { $this->setField('invoice_line_id', $p); }
	public function getInvoiceLineId() { return $this->getField('invoice_line_id'); }
	
		
	public function setBilled($p) { $this->setField('billed', $p); }
	public function getBilled() { return $this->getField('billed'); }
	
		
	public function setDeleted($p) { $this->setField('deleted', $p); }
	public function getDeleted() { return $this->getField('deleted'); }
	
		
	public function setEdited($p) { $this->setField('edited', $p); }
	public function getEdited() { return $this->getField('edited'); }
	
		
	public function setCreated($p) { $this->setField('created', $p); }
	public function getCreated() { return $this->getField('created'); }
	
	
}

