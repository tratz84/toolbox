<?php


namespace base\model\base;


class UserIpBase extends \core\db\DBObject {

	public function __construct($id=null) {
		$this->setResource( 'default' );
		$this->setTableName( 'base__user_ip' );
		$this->setPrimaryKey( 'user_ip_id' );
		$this->setDatabaseFields( array (
  'user_ip_id' => 
  array (
    'Field' => 'user_ip_id',
    'Type' => 'int(11)',
    'Null' => 'NO',
    'Key' => 'PRI',
    'Default' => NULL,
    'Extra' => 'auto_increment',
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
  'ip' => 
  array (
    'Field' => 'ip',
    'Type' => 'varchar(60)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
  ),
) );
		
		if ($id != null)
			$this->setField($this->primaryKey, $id);
	}
	
		
	public function setUserIpId($p) { $this->setField('user_ip_id', $p); }
	public function getUserIpId() { return $this->getField('user_ip_id'); }
	
		
	public function setUserId($p) { $this->setField('user_id', $p); }
	public function getUserId() { return $this->getField('user_id'); }
	
		
	public function setIp($p) { $this->setField('ip', $p); }
	public function getIp() { return $this->getField('ip'); }
	
	
}

