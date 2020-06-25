<?php


namespace base\model\base;


class CronBase extends \core\db\DBObject {

	public function __construct($id=null) {
		$this->setResource( 'default' );
		$this->setTableName( 'base__cron' );
		$this->setPrimaryKey( 'cron_id' );
		$this->setDatabaseFields( array (
  'cron_id' => 
  array (
    'Field' => 'cron_id',
    'Type' => 'int',
    'Null' => 'NO',
    'Key' => 'PRI',
    'Default' => NULL,
    'Extra' => 'auto_increment',
  ),
  'cron_name' => 
  array (
    'Field' => 'cron_name',
    'Type' => 'varchar(128)',
    'Null' => 'YES',
    'Key' => 'UNI',
    'Default' => NULL,
    'Extra' => '',
  ),
  'last_status' => 
  array (
    'Field' => 'last_status',
    'Type' => 'varchar(128)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
  ),
  'last_run' => 
  array (
    'Field' => 'last_run',
    'Type' => 'datetime',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
  ),
  'running' => 
  array (
    'Field' => 'running',
    'Type' => 'tinyint(1)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => '0',
    'Extra' => '',
  ),
) );
		
		if ($id != null)
			$this->setField($this->primaryKey, $id);
	}
	
		
	public function setCronId($p) { $this->setField('cron_id', $p); }
	public function getCronId() { return $this->getField('cron_id'); }
	
		
	public function setCronName($p) { $this->setField('cron_name', $p); }
	public function getCronName() { return $this->getField('cron_name'); }
	
		
	public function setLastStatus($p) { $this->setField('last_status', $p); }
	public function getLastStatus() { return $this->getField('last_status'); }
	
		
	public function setLastRun($p) { $this->setField('last_run', $p); }
	public function getLastRun() { return $this->getField('last_run'); }
	
		
	public function setRunning($p) { $this->setField('running', $p); }
	public function getRunning() { return $this->getField('running'); }
	
	
}

