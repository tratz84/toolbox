<?php


namespace admin\model\base;


class UserCustomerBase extends \core\db\DBObject {

	public function __construct($id=null) {
		$this->setResource( 'admin' );
		$this->setTableName( 'toolbox__user_customer' );
		$this->setPrimaryKey( 'user_customer_id' );
		$this->setDatabaseFields( array (
  'user_customer_id' => 
  array (
    'Field' => 'user_customer_id',
    'Type' => 'int(11)',
    'Null' => 'NO',
    'Key' => 'PRI',
    'Default' => NULL,
    'Extra' => 'auto_increment',
  ),
  'user_id' => 
  array (
    'Field' => 'user_id',
    'Type' => 'int(11)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
  ),
  'customer_id' => 
  array (
    'Field' => 'customer_id',
    'Type' => 'int(11)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
  ),
) );
		
		if ($id != null)
			$this->setField($this->primaryKey, $id);
	}
	
		
	public function setUserCustomerId($p) { $this->setField('user_customer_id', $p); }
	public function getUserCustomerId() { return $this->getField('user_customer_id'); }
	
		
	public function setUserId($p) { $this->setField('user_id', $p); }
	public function getUserId() { return $this->getField('user_id'); }
	
		
	public function setCustomerId($p) { $this->setField('customer_id', $p); }
	public function getCustomerId() { return $this->getField('customer_id'); }
	
	
}

