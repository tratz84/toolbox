<?php


namespace admin\model\base;


class CustomerBase extends \core\db\DBObject {

	public function __construct($id=null) {
		$this->setResource( 'admin' );
		$this->setTableName( 'insights__customer' );
		$this->setPrimaryKey( 'customer_id' );
		$this->setDatabaseFields( array (
  'customer_id' => 
  array (
    'Field' => 'customer_id',
    'Type' => 'int(11)',
    'Null' => 'NO',
    'Key' => 'PRI',
    'Default' => NULL,
    'Extra' => 'auto_increment',
  ),
  'contextName' => 
  array (
    'Field' => 'contextName',
    'Type' => 'varchar(64)',
    'Null' => 'YES',
    'Key' => 'UNI',
    'Default' => NULL,
    'Extra' => '',
  ),
  'databaseName' => 
  array (
    'Field' => 'databaseName',
    'Type' => 'varchar(64)',
    'Null' => 'YES',
    'Key' => 'UNI',
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
  'experimental' => 
  array (
    'Field' => 'experimental',
    'Type' => 'tinyint(1)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => '0',
    'Extra' => '',
  ),
  'active' => 
  array (
    'Field' => 'active',
    'Type' => 'tinyint(1)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => '1',
    'Extra' => '',
  ),
) );
		
		if ($id != null)
			$this->setField($this->primaryKey, $id);
	}
	
		
	public function setCustomerId($p) { $this->setField('customer_id', $p); }
	public function getCustomerId() { return $this->getField('customer_id'); }
	
		
	public function setContextName($p) { $this->setField('contextName', $p); }
	public function getContextName() { return $this->getField('contextName'); }
	
		
	public function setDatabaseName($p) { $this->setField('databaseName', $p); }
	public function getDatabaseName() { return $this->getField('databaseName'); }
	
		
	public function setDescription($p) { $this->setField('description', $p); }
	public function getDescription() { return $this->getField('description'); }
	
		
	public function setNote($p) { $this->setField('note', $p); }
	public function getNote() { return $this->getField('note'); }
	
		
	public function setExperimental($p) { $this->setField('experimental', $p); }
	public function getExperimental() { return $this->getField('experimental'); }
	
		
	public function setActive($p) { $this->setField('active', $p); }
	public function getActive() { return $this->getField('active'); }
	
	
}

