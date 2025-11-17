<?php


namespace invoice\model\base;


class OrderBase extends \core\db\DBObject {

	public function __construct($id=null) {
		$this->setResource( 'default' );
		$this->setTableName( 'invoice__order' );
		$this->setPrimaryKey( 'order_id' );
		$this->setDatabaseFields( array (
  'order_id' => 
  array (
    'Field' => 'order_id',
    'Type' => 'int',
    'Null' => 'NO',
    'Key' => 'PRI',
    'Default' => NULL,
    'Extra' => 'auto_increment',
  ),
  'ref_order_id' => 
  array (
    'Field' => 'ref_order_id',
    'Type' => 'int',
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
  'order_status_id' => 
  array (
    'Field' => 'order_status_id',
    'Type' => 'int',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
  ),
  'order_number' => 
  array (
    'Field' => 'order_number',
    'Type' => 'int',
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
  'order_date' => 
  array (
    'Field' => 'order_date',
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
	
		
	public function setOrderId($p) { $this->setField('order_id', $p); }
	public function getOrderId() { return $this->getField('order_id'); }
	
		
	public function setRefOrderId($p) { $this->setField('ref_order_id', $p); }
	public function getRefOrderId() { return $this->getField('ref_order_id'); }
	
		
	public function setCompanyId($p) { $this->setField('company_id', $p); }
	public function getCompanyId() { return $this->getField('company_id'); }
	
		
	public function setPersonId($p) { $this->setField('person_id', $p); }
	public function getPersonId() { return $this->getField('person_id'); }
	
		
	public function setOrderStatusId($p) { $this->setField('order_status_id', $p); }
	public function getOrderStatusId() { return $this->getField('order_status_id'); }
	
		
	public function setOrderNumber($p) { $this->setField('order_number', $p); }
	public function getOrderNumber() { return $this->getField('order_number'); }
	
		
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
	
		
	public function setOrderDate($p) { $this->setField('order_date', $p); }
	public function getOrderDate() { return $this->getField('order_date'); }
	
		
	public function setEdited($p) { $this->setField('edited', $p); }
	public function getEdited() { return $this->getField('edited'); }
	
		
	public function setCreated($p) { $this->setField('created', $p); }
	public function getCreated() { return $this->getField('created'); }
	
	
}

