<?php


namespace filesync\model\base;


class StoreFileMetaBase extends \core\db\DBObject {

	public function __construct($id=null) {
		$this->setResource( 'default' );
		$this->setTableName( 'filesync__store_file_meta' );
		$this->setPrimaryKey( 'store_file_meta_id' );
		$this->setDatabaseFields( array (
  'store_file_meta_id' => 
  array (
    'Field' => 'store_file_meta_id',
    'Type' => 'bigint(20)',
    'Null' => 'NO',
    'Key' => 'PRI',
    'Default' => NULL,
    'Extra' => 'auto_increment',
  ),
  'store_file_id' => 
  array (
    'Field' => 'store_file_id',
    'Type' => 'bigint(20)',
    'Null' => 'YES',
    'Key' => 'UNI',
    'Default' => NULL,
    'Extra' => '',
  ),
  'company_id' => 
  array (
    'Field' => 'company_id',
    'Type' => 'int(11)',
    'Null' => 'YES',
    'Key' => 'MUL',
    'Default' => NULL,
    'Extra' => '',
  ),
  'person_id' => 
  array (
    'Field' => 'person_id',
    'Type' => 'int(11)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
  ),
  'subject' => 
  array (
    'Field' => 'subject',
    'Type' => 'varchar(255)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
  ),
  'long_description' => 
  array (
    'Field' => 'long_description',
    'Type' => 'text',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
  ),
  'document_date' => 
  array (
    'Field' => 'document_date',
    'Type' => 'date',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
  ),
) );
		
		if ($id != null)
			$this->setField($this->primaryKey, $id);
	}
	
		
	public function setStoreFileMetaId($p) { $this->setField('store_file_meta_id', $p); }
	public function getStoreFileMetaId() { return $this->getField('store_file_meta_id'); }
	
		
	public function setStoreFileId($p) { $this->setField('store_file_id', $p); }
	public function getStoreFileId() { return $this->getField('store_file_id'); }
	
		
	public function setCompanyId($p) { $this->setField('company_id', $p); }
	public function getCompanyId() { return $this->getField('company_id'); }
	
		
	public function setPersonId($p) { $this->setField('person_id', $p); }
	public function getPersonId() { return $this->getField('person_id'); }
	
		
	public function setSubject($p) { $this->setField('subject', $p); }
	public function getSubject() { return $this->getField('subject'); }
	
		
	public function setLongDescription($p) { $this->setField('long_description', $p); }
	public function getLongDescription() { return $this->getField('long_description'); }
	
		
	public function setDocumentDate($p) { $this->setField('document_date', $p); }
	public function getDocumentDate() { return $this->getField('document_date'); }
	
	
}

