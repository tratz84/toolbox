<?php


namespace invoice\model\base;


class OfferFileBase extends \core\db\DBObject {

	public function __construct($id=null) {
		$this->setResource( 'default' );
		$this->setTableName( 'invoice__offer_file' );
		$this->setPrimaryKey( 'offer_file_id' );
		$this->setDatabaseFields( array (
  'offer_file_id' => 
  array (
    'Field' => 'offer_file_id',
    'Type' => 'int(11)',
    'Null' => 'NO',
    'Key' => 'PRI',
    'Default' => NULL,
    'Extra' => 'auto_increment',
  ),
  'offer_id' => 
  array (
    'Field' => 'offer_id',
    'Type' => 'int(11)',
    'Null' => 'YES',
    'Key' => 'MUL',
    'Default' => NULL,
    'Extra' => '',
  ),
  'file_id' => 
  array (
    'Field' => 'file_id',
    'Type' => 'int(11)',
    'Null' => 'YES',
    'Key' => 'MUL',
    'Default' => NULL,
    'Extra' => '',
  ),
  'sort' => 
  array (
    'Field' => 'sort',
    'Type' => 'int(11)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
  ),
) );
		
		if ($id != null)
			$this->setField($this->primaryKey, $id);
	}
	
		
	public function setOfferFileId($p) { $this->setField('offer_file_id', $p); }
	public function getOfferFileId() { return $this->getField('offer_file_id'); }
	
		
	public function setOfferId($p) { $this->setField('offer_id', $p); }
	public function getOfferId() { return $this->getField('offer_id'); }
	
		
	public function setFileId($p) { $this->setField('file_id', $p); }
	public function getFileId() { return $this->getField('file_id'); }
	
		
	public function setSort($p) { $this->setField('sort', $p); }
	public function getSort() { return $this->getField('sort'); }
	
	
}

