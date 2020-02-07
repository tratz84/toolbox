<?php


namespace payment\model\base;


class PaymentImportBase extends \core\db\DBObject {

	public function __construct($id=null) {
		$this->setResource( 'default' );
		$this->setTableName( 'payment__payment_import' );
		$this->setPrimaryKey( 'payment_import_id' );
		$this->setDatabaseFields( array (
  'payment_import_id' => 
  array (
    'Field' => 'payment_import_id',
    'Type' => 'int',
    'Null' => 'NO',
    'Key' => 'PRI',
    'Default' => NULL,
    'Extra' => 'auto_increment',
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
  'status' => 
  array (
    'Field' => 'status',
    'Type' => 'varchar(16)',
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
	
		
	public function setPaymentImportId($p) { $this->setField('payment_import_id', $p); }
	public function getPaymentImportId() { return $this->getField('payment_import_id'); }
	
		
	public function setDescription($p) { $this->setField('description', $p); }
	public function getDescription() { return $this->getField('description'); }
	
		
	public function setStatus($p) { $this->setField('status', $p); }
	public function getStatus() { return $this->getField('status'); }
	
		
	public function setCreated($p) { $this->setField('created', $p); }
	public function getCreated() { return $this->getField('created'); }
	
	
}

