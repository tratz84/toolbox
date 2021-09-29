<?php


namespace fail2ban\model\base;


class BanCheckBase extends \core\db\DBObject {

	public function __construct($id=null) {
		$this->setResource( 'default' );
		$this->setTableName( 'fail2ban__ban_check' );
		$this->setPrimaryKey( 'ban_check_id' );
		$this->setDatabaseFields( array (
  'ban_check_id' => 
  array (
    'Field' => 'ban_check_id',
    'Type' => 'int',
    'Null' => 'NO',
    'Key' => 'PRI',
    'Default' => NULL,
    'Extra' => 'auto_increment',
  ),
  'ip' => 
  array (
    'Field' => 'ip',
    'Type' => 'varchar(64)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
  ),
  'message' => 
  array (
    'Field' => 'message',
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
	
		
	public function setBanCheckId($p) { $this->setField('ban_check_id', $p); }
	public function getBanCheckId() { return $this->getField('ban_check_id'); }
	
		
	public function setIp($p) { $this->setField('ip', $p); }
	public function getIp() { return $this->getField('ip'); }
	
		
	public function setMessage($p) { $this->setField('message', $p); }
	public function getMessage() { return $this->getField('message'); }
	
		
	public function setCreated($p) { $this->setField('created', $p); }
	public function getCreated() { return $this->getField('created'); }
	
	
}

