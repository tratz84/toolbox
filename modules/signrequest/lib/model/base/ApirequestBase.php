<?php


namespace signrequest\model\base;


class ApirequestBase extends \core\db\DBObject {

	public function __construct($id=null) {
		$this->setResource( 'default' );
		$this->setTableName( 'signrequest__apirequest' );
		$this->setPrimaryKey( 'apirequest_id' );
		$this->setDatabaseFields( array (
  'apirequest_id' => 
  array (
    'Field' => 'apirequest_id',
    'Type' => 'int(11)',
    'Null' => 'NO',
    'Key' => 'PRI',
    'Default' => NULL,
    'Extra' => 'auto_increment',
  ),
  'ref' => 
  array (
    'Field' => 'ref',
    'Type' => 'varchar(64)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
  ),
  'url' => 
  array (
    'Field' => 'url',
    'Type' => 'varchar(128)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
  ),
  'data_sent' => 
  array (
    'Field' => 'data_sent',
    'Type' => 'text',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
  ),
  'data_received' => 
  array (
    'Field' => 'data_received',
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
	
		
	public function setApirequestId($p) { $this->setField('apirequest_id', $p); }
	public function getApirequestId() { return $this->getField('apirequest_id'); }
	
		
	public function setRef($p) { $this->setField('ref', $p); }
	public function getRef() { return $this->getField('ref'); }
	
		
	public function setUrl($p) { $this->setField('url', $p); }
	public function getUrl() { return $this->getField('url'); }
	
		
	public function setDataSent($p) { $this->setField('data_sent', $p); }
	public function getDataSent() { return $this->getField('data_sent'); }
	
		
	public function setDataReceived($p) { $this->setField('data_received', $p); }
	public function getDataReceived() { return $this->getField('data_received'); }
	
		
	public function setCreated($p) { $this->setField('created', $p); }
	public function getCreated() { return $this->getField('created'); }
	
	
}

