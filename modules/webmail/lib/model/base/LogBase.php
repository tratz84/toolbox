<?php


namespace webmail\model\base;


class LogBase extends \core\db\DBObject {

	public function __construct($id=null) {
		$this->setResource( 'default' );
		$this->setTableName( 'mailing__log' );
		$this->setPrimaryKey( 'log_id' );
		$this->setDatabaseFields( array (
  'log_id' => 
  array (
    'Field' => 'log_id',
    'Type' => 'int(11)',
    'Null' => 'NO',
    'Key' => 'PRI',
    'Default' => NULL,
    'Extra' => 'auto_increment',
  ),
  'template_id' => 
  array (
    'Field' => 'template_id',
    'Type' => 'int(11)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
  ),
  'from_name' => 
  array (
    'Field' => 'from_name',
    'Type' => 'varchar(255)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
  ),
  'from_email' => 
  array (
    'Field' => 'from_email',
    'Type' => 'varchar(255)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
  ),
  'log_to' => 
  array (
    'Field' => 'log_to',
    'Type' => 'text',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
  ),
  'log_cc' => 
  array (
    'Field' => 'log_cc',
    'Type' => 'text',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
  ),
  'log_bcc' => 
  array (
    'Field' => 'log_bcc',
    'Type' => 'text',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
  ),
  'subject' => 
  array (
    'Field' => 'subject',
    'Type' => 'varchar(512)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
  ),
  'content' => 
  array (
    'Field' => 'content',
    'Type' => 'text',
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
	
		
	public function setLogId($p) { $this->setField('log_id', $p); }
	public function getLogId() { return $this->getField('log_id'); }
	
		
	public function setTemplateId($p) { $this->setField('template_id', $p); }
	public function getTemplateId() { return $this->getField('template_id'); }
	
		
	public function setFromName($p) { $this->setField('from_name', $p); }
	public function getFromName() { return $this->getField('from_name'); }
	
		
	public function setFromEmail($p) { $this->setField('from_email', $p); }
	public function getFromEmail() { return $this->getField('from_email'); }
	
		
	public function setLogTo($p) { $this->setField('log_to', $p); }
	public function getLogTo() { return $this->getField('log_to'); }
	
		
	public function setLogCc($p) { $this->setField('log_cc', $p); }
	public function getLogCc() { return $this->getField('log_cc'); }
	
		
	public function setLogBcc($p) { $this->setField('log_bcc', $p); }
	public function getLogBcc() { return $this->getField('log_bcc'); }
	
		
	public function setSubject($p) { $this->setField('subject', $p); }
	public function getSubject() { return $this->getField('subject'); }
	
		
	public function setContent($p) { $this->setField('content', $p); }
	public function getContent() { return $this->getField('content'); }
	
		
	public function setCreated($p) { $this->setField('created', $p); }
	public function getCreated() { return $this->getField('created'); }
	
	
}

