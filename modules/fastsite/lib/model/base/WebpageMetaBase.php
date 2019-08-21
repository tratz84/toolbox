<?php


namespace fastsite\model\base;


class WebpageMetaBase extends \core\db\DBObject {

	public function __construct($id=null) {
		$this->setResource( 'default' );
		$this->setTableName( 'fastsite__webpage_meta' );
		$this->setPrimaryKey( 'webpage_meta_id' );
		$this->setDatabaseFields( array (
  'webpage_meta_id' => 
  array (
    'Field' => 'webpage_meta_id',
    'Type' => 'int(11)',
    'Null' => 'NO',
    'Key' => 'PRI',
    'Default' => NULL,
    'Extra' => 'auto_increment',
  ),
  'page_no' => 
  array (
    'Field' => 'page_no',
    'Type' => 'int(11)',
    'Null' => 'YES',
    'Key' => 'MUL',
    'Default' => NULL,
    'Extra' => '',
  ),
  'meta_key' => 
  array (
    'Field' => 'meta_key',
    'Type' => 'varchar(64)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
  ),
  'meta_value' => 
  array (
    'Field' => 'meta_value',
    'Type' => 'text',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
  ),
) );
		
		if ($id != null)
			$this->setField($this->primaryKey, $id);
	}
	
		
	public function setWebpageMetaId($p) { $this->setField('webpage_meta_id', $p); }
	public function getWebpageMetaId() { return $this->getField('webpage_meta_id'); }
	
		
	public function setPageNo($p) { $this->setField('page_no', $p); }
	public function getPageNo() { return $this->getField('page_no'); }
	
		
	public function setMetaKey($p) { $this->setField('meta_key', $p); }
	public function getMetaKey() { return $this->getField('meta_key'); }
	
		
	public function setMetaValue($p) { $this->setField('meta_value', $p); }
	public function getMetaValue() { return $this->getField('meta_value'); }
	
	
}

