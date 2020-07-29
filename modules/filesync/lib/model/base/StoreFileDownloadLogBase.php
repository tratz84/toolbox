<?php


namespace filesync\model\base;


class StoreFileDownloadLogBase extends \core\db\DBObject {

	public function __construct($id=null) {
		$this->setResource( 'default' );
		$this->setTableName( 'filesync__store_file_download_log' );
		$this->setPrimaryKey( 'store_file_download_log_id' );
		$this->setDatabaseFields( array (
  'store_file_download_log_id' => 
  array (
    'Field' => 'store_file_download_log_id',
    'Type' => 'bigint',
    'Null' => 'NO',
    'Key' => 'PRI',
    'Default' => NULL,
    'Extra' => 'auto_increment',
  ),
  'store_file_id' => 
  array (
    'Field' => 'store_file_id',
    'Type' => 'bigint',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
  ),
  'ip' => 
  array (
    'Field' => 'ip',
    'Type' => 'varchar(40)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
  ),
  'dump' => 
  array (
    'Field' => 'dump',
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
	
		
	public function setStoreFileDownloadLogId($p) { $this->setField('store_file_download_log_id', $p); }
	public function getStoreFileDownloadLogId() { return $this->getField('store_file_download_log_id'); }
	
		
	public function setStoreFileId($p) { $this->setField('store_file_id', $p); }
	public function getStoreFileId() { return $this->getField('store_file_id'); }
	
		
	public function setIp($p) { $this->setField('ip', $p); }
	public function getIp() { return $this->getField('ip'); }
	
		
	public function setDump($p) { $this->setField('dump', $p); }
	public function getDump() { return $this->getField('dump'); }
	
		
	public function setCreated($p) { $this->setField('created', $p); }
	public function getCreated() { return $this->getField('created'); }
	
	
}

