<?php


namespace invoice\model\base;


class PaymentBase extends \core\db\DBObject {

	public function __construct($id=null) {
		$this->setResource( 'default' );
		$this->setTableName( 'invoice__payment' );
		$this->setPrimaryKey( 'payment_id' );
		$this->setDatabaseFields( array (
  'payment_id' => 
  array (
    'Field' => 'payment_id',
    'Type' => 'int(11)',
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
    'Type' => 'int(11)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
  ),
  'payment_method_id' => 
  array (
    'Field' => 'payment_method_id',
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
  'company_id' => 
  array (
    'Field' => 'company_id',
    'Type' => 'int(11)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
  ),
  'invoice_id' => 
  array (
    'Field' => 'invoice_id',
    'Type' => 'int(11)',
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
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
  ),
  'description' => 
  array (
    'Field' => 'description',
    'Type' => 'varchar(255)',
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
  'amount' => 
  array (
    'Field' => 'amount',
    'Type' => 'decimal(10,2)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
  ),
  'payment_type' => 
  array (
    'Field' => 'payment_type',
    'Type' => 'varchar(32)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
  ),
  'payment_date' => 
  array (
    'Field' => 'payment_date',
    'Type' => 'date',
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
	
		
	public function setPaymentId($p) { $this->setField('payment_id', $p); }
	public function getPaymentId() { return $this->getField('payment_id'); }
	
		
	public function setRefObject($p) { $this->setField('ref_object', $p); }
	public function getRefObject() { return $this->getField('ref_object'); }
	
		
	public function setRefId($p) { $this->setField('ref_id', $p); }
	public function getRefId() { return $this->getField('ref_id'); }
	
		
	public function setPaymentMethodId($p) { $this->setField('payment_method_id', $p); }
	public function getPaymentMethodId() { return $this->getField('payment_method_id'); }
	
		
	public function setPersonId($p) { $this->setField('person_id', $p); }
	public function getPersonId() { return $this->getField('person_id'); }
	
		
	public function setCompanyId($p) { $this->setField('company_id', $p); }
	public function getCompanyId() { return $this->getField('company_id'); }
	
		
	public function setInvoiceId($p) { $this->setField('invoice_id', $p); }
	public function getInvoiceId() { return $this->getField('invoice_id'); }
	
		
	public function setInvoiceLineId($p) { $this->setField('invoice_line_id', $p); }
	public function getInvoiceLineId() { return $this->getField('invoice_line_id'); }
	
		
	public function setDescription($p) { $this->setField('description', $p); }
	public function getDescription() { return $this->getField('description'); }
	
		
	public function setNote($p) { $this->setField('note', $p); }
	public function getNote() { return $this->getField('note'); }
	
		
	public function setAmount($p) { $this->setField('amount', $p); }
	public function getAmount() { return $this->getField('amount'); }
	
		
	public function setPaymentType($p) { $this->setField('payment_type', $p); }
	public function getPaymentType() { return $this->getField('payment_type'); }
	
		
	public function setPaymentDate($p) { $this->setField('payment_date', $p); }
	public function getPaymentDate() { return $this->getField('payment_date'); }
	
		
	public function setCreated($p) { $this->setField('created', $p); }
	public function getCreated() { return $this->getField('created'); }
	
	
}

