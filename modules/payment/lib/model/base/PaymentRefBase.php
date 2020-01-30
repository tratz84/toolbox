<?php


namespace payment\model\base;


class PaymentRefBase extends \core\db\DBObject {

	public function __construct($id=null) {
		$this->setResource( 'default' );
		$this->setTableName( 'payment__payment_ref' );
		$this->setPrimaryKey( 'payment_ref_id' );
		$this->setDatabaseFields( array (
  'payment_ref_id' => 
  array (
    'Field' => 'payment_ref_id',
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
	
		
	public function setPaymentRefId($p) { $this->setField('payment_ref_id', $p); }
	public function getPaymentRefId() { return $this->getField('payment_ref_id'); }
	
		
	public function setPaymentId($p) { $this->setField('payment_id', $p); }
	public function getPaymentId() { return $this->getField('payment_id'); }
	
		
	public function setRefObject($p) { $this->setField('ref_object', $p); }
	public function getRefObject() { return $this->getField('ref_object'); }
	
		
	public function setRefId($p) { $this->setField('ref_id', $p); }
	public function getRefId() { return $this->getField('ref_id'); }
	
	
}

