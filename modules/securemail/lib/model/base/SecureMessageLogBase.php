<?php


namespace securemail\model\base;


class SecureMessageLogBase extends \core\db\DBObject {

	public function __construct($id=null) {
		$this->setResource( 'default' );
		$this->setTableName( 'securemail__secure_message_log' );
		$this->setPrimaryKey( 'secure_message_log_id' );
		$this->setDatabaseFields( array (
  'secure_message_log_id' => 
  array (
    'Field' => 'secure_message_log_id',
    'Type' => 'int',
    'Null' => 'NO',
    'Key' => 'PRI',
    'Default' => NULL,
    'Extra' => 'auto_increment',
  ),
  'secure_message_id' => 
  array (
    'Field' => 'secure_message_id',
    'Type' => 'int',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
  ),
  'opened' => 
  array (
    'Field' => 'opened',
    'Type' => 'tinyint(1)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
  ),
  'log_message' => 
  array (
    'Field' => 'log_message',
    'Type' => 'varchar(64)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
  ),
  'ip' => 
  array (
    'Field' => 'ip',
    'Type' => 'varchar(46)',
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
	
		
	public function setSecureMessageLogId($p) { $this->setField('secure_message_log_id', $p); }
	public function getSecureMessageLogId() { return $this->getField('secure_message_log_id'); }
	
		
	public function setSecureMessageId($p) { $this->setField('secure_message_id', $p); }
	public function getSecureMessageId() { return $this->getField('secure_message_id'); }
	
		
	public function setOpened($p) { $this->setField('opened', $p); }
	public function getOpened() { return $this->getField('opened'); }
	
		
	public function setLogMessage($p) { $this->setField('log_message', $p); }
	public function getLogMessage() { return $this->getField('log_message'); }
	
		
	public function setIp($p) { $this->setField('ip', $p); }
	public function getIp() { return $this->getField('ip'); }
	
		
	public function setCreated($p) { $this->setField('created', $p); }
	public function getCreated() { return $this->getField('created'); }
	
	
}

