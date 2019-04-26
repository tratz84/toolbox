<?php


namespace invoice\model\base;


class InvoiceBase extends \core\db\DBObject {

	public function __construct($id=null) {
		$this->setResource( 'default' );
		$this->setTableName( 'invoice__invoice' );
		$this->setPrimaryKey( 'invoice_id' );
		$this->setDatabaseFields( array (
  'invoice_id' => 
  array (
    'Field' => 'invoice_id',
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
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
  ),
  'invoice_status_id' => 
  array (
    'Field' => 'invoice_status_id',
    'Type' => 'int(11)',
    'Null' => 'YES',
    'Key' => 'MUL',
    'Default' => NULL,
    'Extra' => '',
  ),
  'tax_shift' => 
  array (
    'Field' => 'tax_shift',
    'Type' => 'tinyint(1)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => '0',
    'Extra' => '',
  ),
  'invoice_number' => 
  array (
    'Field' => 'invoice_number',
    'Type' => 'int(11)',
    'Null' => 'YES',
    'Key' => 'UNI',
    'Default' => NULL,
    'Extra' => '',
  ),
  'subject' => 
  array (
    'Field' => 'subject',
    'Type' => 'varchar(255)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
  ),
  'comment' => 
  array (
    'Field' => 'comment',
    'Type' => 'mediumtext',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
  ),
  'note' => 
  array (
    'Field' => 'note',
    'Type' => 'text',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
  ),
  'total_calculated_price' => 
  array (
    'Field' => 'total_calculated_price',
    'Type' => 'decimal(10,2)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
  ),
  'total_calculated_price_incl_vat' => 
  array (
    'Field' => 'total_calculated_price_incl_vat',
    'Type' => 'decimal(10,2)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
  ),
  'invoice_date' => 
  array (
    'Field' => 'invoice_date',
    'Type' => 'date',
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
	
		
	public function setInvoiceId($p) { $this->setField('invoice_id', $p); }
	public function getInvoiceId() { return $this->getField('invoice_id'); }
	
		
	public function setCompanyId($p) { $this->setField('company_id', $p); }
	public function getCompanyId() { return $this->getField('company_id'); }
	
		
	public function setPersonId($p) { $this->setField('person_id', $p); }
	public function getPersonId() { return $this->getField('person_id'); }
	
		
	public function setInvoiceStatusId($p) { $this->setField('invoice_status_id', $p); }
	public function getInvoiceStatusId() { return $this->getField('invoice_status_id'); }
	
		
	public function setTaxShift($p) { $this->setField('tax_shift', $p); }
	public function getTaxShift() { return $this->getField('tax_shift'); }
	
		
	public function setInvoiceNumber($p) { $this->setField('invoice_number', $p); }
	public function getInvoiceNumber() { return $this->getField('invoice_number'); }
	
		
	public function setSubject($p) { $this->setField('subject', $p); }
	public function getSubject() { return $this->getField('subject'); }
	
		
	public function setComment($p) { $this->setField('comment', $p); }
	public function getComment() { return $this->getField('comment'); }
	
		
	public function setNote($p) { $this->setField('note', $p); }
	public function getNote() { return $this->getField('note'); }
	
		
	public function setTotalCalculatedPrice($p) { $this->setField('total_calculated_price', $p); }
	public function getTotalCalculatedPrice() { return $this->getField('total_calculated_price'); }
	
		
	public function setTotalCalculatedPriceInclVat($p) { $this->setField('total_calculated_price_incl_vat', $p); }
	public function getTotalCalculatedPriceInclVat() { return $this->getField('total_calculated_price_incl_vat'); }
	
		
	public function setInvoiceDate($p) { $this->setField('invoice_date', $p); }
	public function getInvoiceDate() { return $this->getField('invoice_date'); }
	
		
	public function setEdited($p) { $this->setField('edited', $p); }
	public function getEdited() { return $this->getField('edited'); }
	
		
	public function setCreated($p) { $this->setField('created', $p); }
	public function getCreated() { return $this->getField('created'); }
	
	
}

