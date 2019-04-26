<?php


namespace webmail\model\base;


class EmailFileBase extends \core\db\DBObject {

	public function __construct($id=null) {
		$this->setResource( 'default' );
		$this->setTableName( 'webmail__email_file' );
		$this->setPrimaryKey( 'email_file_id' );
		$this->setDatabaseFields( array (
  'email_file_id' => 
  array (
    'Field' => 'email_file_id',
    'Type' => 'int(11)',
    'Null' => 'NO',
    'Key' => 'PRI',
    'Default' => NULL,
    'Extra' => 'auto_increment',
  ),
  'email_id' => 
  array (
    'Field' => 'email_id',
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
	
		
	public function setEmailFileId($p) { $this->setField('email_file_id', $p); }
	public function getEmailFileId() { return $this->getField('email_file_id'); }
	
		
	public function setEmailId($p) { $this->setField('email_id', $p); }
	public function getEmailId() { return $this->getField('email_id'); }
	
		
	public function setFilename($p) { $this->setField('filename', $p); }
	public function getFilename() { return $this->getField('filename'); }
	
		
	public function setPath($p) { $this->setField('path', $p); }
	public function getPath() { return $this->getField('path'); }
	
	
}

