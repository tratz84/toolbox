<?php


namespace base\model\base;


class CronRunBase extends \core\db\DBObject {

	public function __construct($id=null) {
		$this->setResource( 'default' );
		$this->setTableName( 'base__cron_run' );
		$this->setPrimaryKey( 'cron_run_id' );
		$this->setDatabaseFields( array (
  'cron_run_id' => 
  array (
    'Field' => 'cron_run_id',
    'Type' => 'int(11)',
    'Null' => 'NO',
    'Key' => 'PRI',
    'Default' => NULL,
    'Extra' => 'auto_increment',
  ),
  'cron_id' => 
  array (
    'Field' => 'cron_id',
    'Type' => 'int(11)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
  ),
  'message' => 
  array (
    'Field' => 'message',
    'Type' => 'text',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
  ),
  'error' => 
  array (
    'Field' => 'error',
    'Type' => 'text',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
  ),
  'status' => 
  array (
    'Field' => 'status',
    'Type' => 'varchar(64)',
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
	
		
	public function setCronRunId($p) { $this->setField('cron_run_id', $p); }
	public function getCronRunId() { return $this->getField('cron_run_id'); }
	
		
	public function setCronId($p) { $this->setField('cron_id', $p); }
	public function getCronId() { return $this->getField('cron_id'); }
	
		
	public function setMessage($p) { $this->setField('message', $p); }
	public function getMessage() { return $this->getField('message'); }
	
		
	public function setError($p) { $this->setField('error', $p); }
	public function getError() { return $this->getField('error'); }
	
		
	public function setStatus($p) { $this->setField('status', $p); }
	public function getStatus() { return $this->getField('status'); }
	
		
	public function setCreated($p) { $this->setField('created', $p); }
	public function getCreated() { return $this->getField('created'); }
	
	
}

