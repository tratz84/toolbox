<?php


namespace invoice\model\base;


class OrderLineBase extends \core\db\DBObject {

	public function __construct($id=null) {
		$this->setResource( 'default' );
		$this->setTableName( 'invoice__order_line' );
		$this->setPrimaryKey( 'order_line_id' );
		$this->setDatabaseFields( array (
  'order_line_id' => 
  array (
    'Field' => 'order_line_id',
    'Type' => 'int',
    'Null' => 'NO',
    'Key' => 'PRI',
    'Default' => NULL,
    'Extra' => 'auto_increment',
  ),
  'order_id' => 
  array (
    'Field' => 'order_id',
    'Type' => 'int',
    'Null' => 'YES',
    'Key' => 'MUL',
    'Default' => NULL,
    'Extra' => '',
  ),
  'article_id' => 
  array (
    'Field' => 'article_id',
    'Type' => 'int',
    'Null' => 'YES',
    'Key' => '',
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
  'amount' => 
  array (
    'Field' => 'amount',
    'Type' => 'double',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
  ),
  'vat_percentage' => 
  array (
    'Field' => 'vat_percentage',
    'Type' => 'double',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
  ),
  'vat_amount' => 
  array (
    'Field' => 'vat_amount',
    'Type' => 'decimal(10,2)',
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
  'short_description2' => 
  array (
    'Field' => 'short_description2',
    'Type' => 'varchar(255)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
  ),
  'long_description' => 
  array (
    'Field' => 'long_description',
    'Type' => 'mediumtext',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
  ),
  'line_type' => 
  array (
    'Field' => 'line_type',
    'Type' => 'varchar(16)',
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
) );
		
		if ($id != null)
			$this->setField($this->primaryKey, $id);
	}
	
		
	public function setOrderLineId($p) { $this->setField('order_line_id', $p); }
	public function getOrderLineId() { return $this->getField('order_line_id'); }
	
		
	public function setOrderId($p) { $this->setField('order_id', $p); }
	public function getOrderId() { return $this->getField('order_id'); }
	
		
	public function setArticleId($p) { $this->setField('article_id', $p); }
	public function getArticleId() { return $this->getField('article_id'); }
	
		
	public function setShortDescription($p) { $this->setField('short_description', $p); }
	public function getShortDescription() { return $this->getField('short_description'); }
	
		
	public function setAmount($p) { $this->setField('amount', $p); }
	public function getAmount() { return $this->getField('amount'); }
	
		
	public function setVatPercentage($p) { $this->setField('vat_percentage', $p); }
	public function getVatPercentage() { return $this->getField('vat_percentage'); }
	
		
	public function setVatAmount($p) { $this->setField('vat_amount', $p); }
	public function getVatAmount() { return $this->getField('vat_amount'); }
	
		
	public function setSort($p) { $this->setField('sort', $p); }
	public function getSort() { return $this->getField('sort'); }
	
		
	public function setEdited($p) { $this->setField('edited', $p); }
	public function getEdited() { return $this->getField('edited'); }
	
		
	public function setCreated($p) { $this->setField('created', $p); }
	public function getCreated() { return $this->getField('created'); }
	
		
	public function setShortDescription2($p) { $this->setField('short_description2', $p); }
	public function getShortDescription2() { return $this->getField('short_description2'); }
	
		
	public function setLongDescription($p) { $this->setField('long_description', $p); }
	public function getLongDescription() { return $this->getField('long_description'); }
	
		
	public function setLineType($p) { $this->setField('line_type', $p); }
	public function getLineType() { return $this->getField('line_type'); }
	
		
	public function setPrice($p) { $this->setField('price', $p); }
	public function getPrice() { return $this->getField('price'); }
	
	
}

