<?php


namespace twofaauth\model\base;


class TwoFaCookieBase extends \core\db\DBObject {

	public function __construct($id=null) {
		$this->setResource( 'default' );
		$this->setTableName( 'twofaauth__two_fa_cookie' );
		$this->setPrimaryKey( 'cookie_id' );
		$this->setDatabaseFields( array (
  'cookie_id' => 
  array (
    'Field' => 'cookie_id',
    'Type' => 'int',
    'Null' => 'NO',
    'Key' => 'PRI',
    'Default' => NULL,
    'Extra' => 'auto_increment',
  ),
  'cookie_value' => 
  array (
    'Field' => 'cookie_value',
    'Type' => 'varchar(255)',
    'Null' => 'YES',
    'Key' => 'MUL',
    'Default' => NULL,
    'Extra' => '',
  ),
  'secret_key' => 
  array (
    'Field' => 'secret_key',
    'Type' => 'varchar(32)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
  ),
  'activated' => 
  array (
    'Field' => 'activated',
    'Type' => 'tinyint(1)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
  ),
  'last_visit' => 
  array (
    'Field' => 'last_visit',
    'Type' => 'datetime',
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
	
		
	public function setCookieId($p) { $this->setField('cookie_id', $p); }
	public function getCookieId() { return $this->getField('cookie_id'); }
	
		
	public function setCookieValue($p) { $this->setField('cookie_value', $p); }
	public function getCookieValue() { return $this->getField('cookie_value'); }
	
		
	public function setSecretKey($p) { $this->setField('secret_key', $p); }
	public function getSecretKey() { return $this->getField('secret_key'); }
	
		
	public function setActivated($p) { $this->setField('activated', $p); }
	public function getActivated() { return $this->getField('activated'); }
	
		
	public function setLastVisit($p) { $this->setField('last_visit', $p); }
	public function getLastVisit() { return $this->getField('last_visit'); }
	
		
	public function setCreated($p) { $this->setField('created', $p); }
	public function getCreated() { return $this->getField('created'); }
	
	
}

