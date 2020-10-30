<?php


namespace admin\model\base;


class ExceptionLogBase extends \core\db\DBObject {

	public function __construct($id=null) {
		$this->setResource( 'admin' );
		$this->setTableName( 'toolbox__exception_log' );
		$this->setPrimaryKey( 'exception_log_id' );
		$this->setDatabaseFields( array (
  'exception_log_id' => 
  array (
    'Field' => 'exception_log_id',
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
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
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
  'request_uri' => 
  array (
    'Field' => 'request_uri',
    'Type' => 'varchar(255)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
  ),
  'message' => 
  array (
    'Field' => 'message',
    'Type' => 'varchar(255)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
  ),
  'stacktrace' => 
  array (
    'Field' => 'stacktrace',
    'Type' => 'mediumtext',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
  ),
  'parameters' => 
  array (
    'Field' => 'parameters',
    'Type' => 'mediumtext',
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
	
		
	public function setExceptionLogId($p) { $this->setField('exception_log_id', $p); }
	public function getExceptionLogId() { return $this->getField('exception_log_id'); }
	
		
	public function setContextName($p) { $this->setField('contextName', $p); }
	public function getContextName() { return $this->getField('contextName'); }
	
		
	public function setUserId($p) { $this->setField('user_id', $p); }
	public function getUserId() { return $this->getField('user_id'); }
	
		
	public function setRequestUri($p) { $this->setField('request_uri', $p); }
	public function getRequestUri() { return $this->getField('request_uri'); }
	
		
	public function setMessage($p) { $this->setField('message', $p); }
	public function getMessage() { return $this->getField('message'); }
	
		
	public function setStacktrace($p) { $this->setField('stacktrace', $p); }
	public function getStacktrace() { return $this->getField('stacktrace'); }
	
		
	public function setParameters($p) { $this->setField('parameters', $p); }
	public function getParameters() { return $this->getField('parameters'); }
	
		
	public function setCreated($p) { $this->setField('created', $p); }
	public function getCreated() { return $this->getField('created'); }
	
	
}

