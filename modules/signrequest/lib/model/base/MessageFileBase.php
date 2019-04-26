<?php


namespace signrequest\model\base;


class MessageFileBase extends \core\db\DBObject {

	public function __construct($id=null) {
		$this->setResource( 'default' );
		$this->setTableName( 'signrequest__message_file' );
		$this->setPrimaryKey( 'message_file_id' );
		$this->setDatabaseFields( array (
  'message_file_id' => 
  array (
    'Field' => 'message_file_id',
    'Type' => 'int(11)',
    'Null' => 'NO',
    'Key' => 'PRI',
    'Default' => NULL,
    'Extra' => 'auto_increment',
  ),
  'message_id' => 
  array (
    'Field' => 'message_id',
    'Type' => 'int(11)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
  ),
  'filename' => 
  array (
    'Field' => 'filename',
    'Type' => 'varchar(255)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
  ),
  'path' => 
  array (
    'Field' => 'path',
    'Type' => 'varchar(255)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
  ),
) );
		
		if ($id != null)
			$this->setField($this->primaryKey, $id);
	}
	
		
	public function setMessageFileId($p) { $this->setField('message_file_id', $p); }
	public function getMessageFileId() { return $this->getField('message_file_id'); }
	
		
	public function setMessageId($p) { $this->setField('message_id', $p); }
	public function getMessageId() { return $this->getField('message_id'); }
	
		
	public function setFilename($p) { $this->setField('filename', $p); }
	public function getFilename() { return $this->getField('filename'); }
	
		
	public function setPath($p) { $this->setField('path', $p); }
	public function getPath() { return $this->getField('path'); }
	
	
}

