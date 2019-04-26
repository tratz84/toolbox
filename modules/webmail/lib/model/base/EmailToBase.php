<?php


namespace webmail\model\base;


class EmailToBase extends \core\db\DBObject {

	public function __construct($id=null) {
		$this->setResource( 'default' );
		$this->setTableName( 'webmail__email_to' );
		$this->setPrimaryKey( 'email_to_id' );
		$this->setDatabaseFields( array (
  'email_to_id' => 
  array (
    'Field' => 'email_to_id',
    'Type' => 'bigint(20)',
    'Null' => 'NO',
    'Key' => 'PRI',
    'Default' => NULL,
    'Extra' => 'auto_increment',
  ),
  'email_id' => 
  array (
    'Field' => 'email_id',
    'Type' => 'int(11)',
    'Null' => 'YES',
    'Key' => 'MUL',
    'Default' => NULL,
    'Extra' => '',
  ),
  'to_type' => 
  array (
    'Field' => 'to_type',
    'Type' => 'enum(\'To\',\'Cc\',\'Bcc\')',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
  ),
  'to_name' => 
  array (
    'Field' => 'to_name',
    'Type' => 'varchar(255)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
  ),
  'to_email' => 
  array (
    'Field' => 'to_email',
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
	
		
	public function setEmailToId($p) { $this->setField('email_to_id', $p); }
	public function getEmailToId() { return $this->getField('email_to_id'); }
	
		
	public function setEmailId($p) { $this->setField('email_id', $p); }
	public function getEmailId() { return $this->getField('email_id'); }
	
		
	public function setToType($p) { $this->setField('to_type', $p); }
	public function getToType() { return $this->getField('to_type'); }
	
		
	public function setToName($p) { $this->setField('to_name', $p); }
	public function getToName() { return $this->getField('to_name'); }
	
		
	public function setToEmail($p) { $this->setField('to_email', $p); }
	public function getToEmail() { return $this->getField('to_email'); }
	
	
}

