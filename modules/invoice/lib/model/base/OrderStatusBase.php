<?php


namespace invoice\model\base;


class OrderStatusBase extends \core\db\DBObject {

	public function __construct($id=null) {
		$this->setResource( 'default' );
		$this->setTableName( 'invoice__order_status' );
		$this->setPrimaryKey( 'order_status_id' );
		$this->setDatabaseFields( array (
  'order_status_id' => 
  array (
    'Field' => 'order_status_id',
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
  'default_selected' => 
  array (
    'Field' => 'default_selected',
    'Type' => 'tinyint(1)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
  ),
  'active' => 
  array (
    'Field' => 'active',
    'Type' => 'tinyint(1)',
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
	
		
	public function setOrderStatusId($p) { $this->setField('order_status_id', $p); }
	public function getOrderStatusId() { return $this->getField('order_status_id'); }
	
		
	public function setDescription($p) { $this->setField('description', $p); }
	public function getDescription() { return $this->getField('description'); }
	
		
	public function setDefaultSelected($p) { $this->setField('default_selected', $p); }
	public function getDefaultSelected() { return $this->getField('default_selected'); }
	
		
	public function setActive($p) { $this->setField('active', $p); }
	public function getActive() { return $this->getField('active'); }
	
		
	public function setSort($p) { $this->setField('sort', $p); }
	public function getSort() { return $this->getField('sort'); }
	
	
}

