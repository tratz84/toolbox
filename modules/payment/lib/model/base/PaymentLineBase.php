<?php


namespace payment\model\base;


class PaymentLineBase extends \core\db\DBObject {

	public function __construct($id=null) {
		$this->setResource( 'default' );
		$this->setTableName( 'payment__payment_line' );
		$this->setPrimaryKey( 'payment_line_id' );
		$this->setDatabaseFields( array (
  'payment_line_id' => 
  array (
    'Field' => 'payment_line_id',
    'Type' => 'int',
    'Null' => 'NO',
    'Key' => 'PRI',
    'Default' => NULL,
    'Extra' => 'auto_increment',
  ),
  'payment_id' => 
  array (
    'Field' => 'payment_id',
    'Type' => 'int',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
  ),
  'payment_method_id' => 
  array (
    'Field' => 'payment_method_id',
    'Type' => 'int',
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
    'Type' => 'varchar(40)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
  ),
  'bankaccountno_contra' => 
  array (
    'Field' => 'bankaccountno_contra',
    'Type' => 'varchar(40)',
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
  'name' => 
  array (
    'Field' => 'name',
    'Type' => 'varchar(255)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
  ),
  'description1' => 
  array (
    'Field' => 'description1',
    'Type' => 'text',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
  ),
  'description2' => 
  array (
    'Field' => 'description2',
    'Type' => 'text',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
  ),
  'mutation_type' => 
  array (
    'Field' => 'mutation_type',
    'Type' => 'varchar(64)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
  ),
  'sort' => 
  array (
    'Field' => 'sort',
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
	
		
	public function setPaymentLineId($p) { $this->setField('payment_line_id', $p); }
	public function getPaymentLineId() { return $this->getField('payment_line_id'); }
	
		
	public function setPaymentId($p) { $this->setField('payment_id', $p); }
	public function getPaymentId() { return $this->getField('payment_id'); }
	
		
	public function setPaymentMethodId($p) { $this->setField('payment_method_id', $p); }
	public function getPaymentMethodId() { return $this->getField('payment_method_id'); }
	
		
	public function setAmount($p) { $this->setField('amount', $p); }
	public function getAmount() { return $this->getField('amount'); }
	
		
	public function setBankaccountno($p) { $this->setField('bankaccountno', $p); }
	public function getBankaccountno() { return $this->getField('bankaccountno'); }
	
		
	public function setBankaccountnoContra($p) { $this->setField('bankaccountno_contra', $p); }
	public function getBankaccountnoContra() { return $this->getField('bankaccountno_contra'); }
	
		
	public function setCode($p) { $this->setField('code', $p); }
	public function getCode() { return $this->getField('code'); }
	
		
	public function setName($p) { $this->setField('name', $p); }
	public function getName() { return $this->getField('name'); }
	
		
	public function setDescription1($p) { $this->setField('description1', $p); }
	public function getDescription1() { return $this->getField('description1'); }
	
		
	public function setDescription2($p) { $this->setField('description2', $p); }
	public function getDescription2() { return $this->getField('description2'); }
	
		
	public function setMutationType($p) { $this->setField('mutation_type', $p); }
	public function getMutationType() { return $this->getField('mutation_type'); }
	
		
	public function setSort($p) { $this->setField('sort', $p); }
	public function getSort() { return $this->getField('sort'); }
	
	
}

