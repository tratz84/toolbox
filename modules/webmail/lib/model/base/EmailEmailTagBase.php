<?php


namespace webmail\model\base;


class EmailEmailTagBase extends \core\db\DBObject {

	public function __construct($id=null) {
		$this->setResource( 'default' );
		$this->setTableName( 'webmail__email_email_tag' );
		$this->setPrimaryKey( 'email_email_tag_id' );
		$this->setDatabaseFields( array (
  'email_email_tag_id' => 
  array (
    'Field' => 'email_email_tag_id',
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
  'email_tag_id' => 
  array (
    'Field' => 'email_tag_id',
    'Type' => 'int(11)',
    'Null' => 'YES',
    'Key' => 'MUL',
    'Default' => NULL,
    'Extra' => '',
  ),
) );
		
		if ($id != null)
			$this->setField($this->primaryKey, $id);
	}
	
		
	public function setEmailEmailTagId($p) { $this->setField('email_email_tag_id', $p); }
	public function getEmailEmailTagId() { return $this->getField('email_email_tag_id'); }
	
		
	public function setEmailId($p) { $this->setField('email_id', $p); }
	public function getEmailId() { return $this->getField('email_id'); }
	
		
	public function setEmailTagId($p) { $this->setField('email_tag_id', $p); }
	public function getEmailTagId() { return $this->getField('email_tag_id'); }
	
	
}

