<?php


namespace payment\model\base;


class PaymentImportLineBase extends \core\db\DBObject {

	public function __construct($id=null) {
		$this->setResource( 'default' );
		$this->setTableName( 'payment__payment_import_line' );
		$this->setPrimaryKey( 'payment_import_line_id' );
		$this->setDatabaseFields( array (
  'payment_import_line_id' => 
  array (
    'Field' => 'payment_import_line_id',
    'Type' => 'int',
    'Null' => 'NO',
    'Key' => 'PRI',
    'Default' => NULL,
    'Extra' => 'auto_increment',
  ),
  'payment_import_id' => 
  array (
    'Field' => 'payment_import_id',
    'Type' => 'int',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
  ),
  'debet_credit' => 
  array (
    'Field' => 'debet_credit',
    'Type' => 'varchar(1)',
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
  'bankaccountno' => 
  array (
    'Field' => 'bankaccountno',
    'Type' => 'varchar(64)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
  ),
  'bankaccountno_contra' => 
  array (
    'Field' => 'bankaccountno_contra',
    'Type' => 'varchar(64)',
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
  'name' => 
  array (
    'Field' => 'name',
    'Type' => 'varchar(255)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
  ),
  'description' => 
  array (
    'Field' => 'description',
    'Type' => 'varchar(512)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
  ),
  'code' => 
  array (
    'Field' => 'code',
    'Type' => 'varchar(16)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
  ),
  'mutation_type' => 
  array (
    'Field' => 'mutation_type',
    'Type' => 'varchar(32)',
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
  'invoice_id' => 
  array (
    'Field' => 'invoice_id',
    'Type' => 'int',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
  ),
  'import_status' => 
  array (
    'Field' => 'import_status',
    'Type' => 'varchar(16)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
  ),
) );
		
		if ($id != null)
			$this->setField($this->primaryKey, $id);
	}
	
		
	public function setPaymentImportLineId($p) { $this->setField('payment_import_line_id', $p); }
	public function getPaymentImportLineId() { return $this->getField('payment_import_line_id'); }
	
		
	public function setPaymentImportId($p) { $this->setField('payment_import_id', $p); }
	public function getPaymentImportId() { return $this->getField('payment_import_id'); }
	
		
	public function setDebetCredit($p) { $this->setField('debet_credit', $p); }
	public function getDebetCredit() { return $this->getField('debet_credit'); }
	
		
	public function setAmount($p) { $this->setField('amount', $p); }
	public function getAmount() { return $this->getField('amount'); }
	
		
	public function setBankaccountno($p) { $this->setField('bankaccountno', $p); }
	public function getBankaccountno() { return $this->getField('bankaccountno'); }
	
		
	public function setBankaccountnoContra($p) { $this->setField('bankaccountno_contra', $p); }
	public function getBankaccountnoContra() { return $this->getField('bankaccountno_contra'); }
	
		
	public function setPaymentDate($p) { $this->setField('payment_date', $p); }
	public function getPaymentDate() { return $this->getField('payment_date'); }
	
		
	public function setName($p) { $this->setField('name', $p); }
	public function getName() { return $this->getField('name'); }
	
		
	public function setDescription($p) { $this->setField('description', $p); }
	public function getDescription() { return $this->getField('description'); }
	
		
	public function setCode($p) { $this->setField('code', $p); }
	public function getCode() { return $this->getField('code'); }
	
		
	public function setMutationType($p) { $this->setField('mutation_type', $p); }
	public function getMutationType() { return $this->getField('mutation_type'); }
	
		
	public function setCompanyId($p) { $this->setField('company_id', $p); }
	public function getCompanyId() { return $this->getField('company_id'); }
	
		
	public function setPersonId($p) { $this->setField('person_id', $p); }
	public function getPersonId() { return $this->getField('person_id'); }
	
		
	public function setInvoiceId($p) { $this->setField('invoice_id', $p); }
	public function getInvoiceId() { return $this->getField('invoice_id'); }
	
		
	public function setImportStatus($p) { $this->setField('import_status', $p); }
	public function getImportStatus() { return $this->getField('import_status'); }
	
	
}

