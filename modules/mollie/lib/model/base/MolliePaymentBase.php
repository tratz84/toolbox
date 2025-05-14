<?php


namespace mollie\model\base;


class MolliePaymentBase extends \core\db\DBObject {

	public function __construct($id=null) {
		$this->setResource( 'default' );
		$this->setTableName( 'mollie__mollie_payment' );
		$this->setPrimaryKey( 'mollie_payment_id' );
		$this->setDatabaseFields( array (
  'mollie_payment_id' => 
  array (
    'Field' => 'mollie_payment_id',
    'Type' => 'varchar(512)',
    'Null' => 'NO',
    'Key' => 'PRI',
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
  'description' => 
  array (
    'Field' => 'description',
    'Type' => 'varchar(512)',
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
  'meta' => 
  array (
    'Field' => 'meta',
    'Type' => 'mediumtext',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
  ),
  'mollie_status' => 
  array (
    'Field' => 'mollie_status',
    'Type' => 'varchar(32)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
  ),
  'return_url' => 
  array (
    'Field' => 'return_url',
    'Type' => 'varchar(255)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
  ),
) );
		
		if ($id != null)
			$this->setField($this->primaryKey, $id);
	}
	
		
	public function setMolliePaymentId($p) { $this->setField('mollie_payment_id', $p); }
	public function getMolliePaymentId() { return $this->getField('mollie_payment_id'); }
	
		
	public function setAmount($p) { $this->setField('amount', $p); }
	public function getAmount() { return $this->getField('amount'); }
	
		
	public function setDescription($p) { $this->setField('description', $p); }
	public function getDescription() { return $this->getField('description'); }
	
		
	public function setEdited($p) { $this->setField('edited', $p); }
	public function getEdited() { return $this->getField('edited'); }
	
		
	public function setCreated($p) { $this->setField('created', $p); }
	public function getCreated() { return $this->getField('created'); }
	
		
	public function setMeta($p) { $this->setField('meta', $p); }
	public function getMeta() { return $this->getField('meta'); }
	
		
	public function setMollieStatus($p) { $this->setField('mollie_status', $p); }
	public function getMollieStatus() { return $this->getField('mollie_status'); }
	
		
	public function setReturnUrl($p) { $this->setField('return_url', $p); }
	public function getReturnUrl() { return $this->getField('return_url'); }
	
	
}

