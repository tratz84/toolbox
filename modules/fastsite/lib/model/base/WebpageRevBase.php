<?php


namespace fastsite\model\base;


class WebpageRevBase extends \core\db\DBObject {

	public function __construct($id=null) {
		$this->setResource( 'default' );
		$this->setTableName( 'fastsite__webpage_rev' );
		$this->setPrimaryKey( 'webpage_rev_id' );
		$this->setDatabaseFields( array (
  'webpage_rev_id' => 
  array (
    'Field' => 'webpage_rev_id',
    'Type' => 'int(11)',
    'Null' => 'NO',
    'Key' => 'PRI',
    'Default' => NULL,
    'Extra' => 'auto_increment',
  ),
  'webpage_id' => 
  array (
    'Field' => 'webpage_id',
    'Type' => 'int(11)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
  ),
  'rev' => 
  array (
    'Field' => 'rev',
    'Type' => 'int(11)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
  ),
  'meta_title' => 
  array (
    'Field' => 'meta_title',
    'Type' => 'text',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
  ),
  'meta_description' => 
  array (
    'Field' => 'meta_description',
    'Type' => 'text',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
  ),
  'meta_keywords' => 
  array (
    'Field' => 'meta_keywords',
    'Type' => 'text',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
  ),
  'content1' => 
  array (
    'Field' => 'content1',
    'Type' => 'text',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
  ),
  'content2' => 
  array (
    'Field' => 'content2',
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
	
		
	public function setWebpageRevId($p) { $this->setField('webpage_rev_id', $p); }
	public function getWebpageRevId() { return $this->getField('webpage_rev_id'); }
	
		
	public function setWebpageId($p) { $this->setField('webpage_id', $p); }
	public function getWebpageId() { return $this->getField('webpage_id'); }
	
		
	public function setRev($p) { $this->setField('rev', $p); }
	public function getRev() { return $this->getField('rev'); }
	
		
	public function setMetaTitle($p) { $this->setField('meta_title', $p); }
	public function getMetaTitle() { return $this->getField('meta_title'); }
	
		
	public function setMetaDescription($p) { $this->setField('meta_description', $p); }
	public function getMetaDescription() { return $this->getField('meta_description'); }
	
		
	public function setMetaKeywords($p) { $this->setField('meta_keywords', $p); }
	public function getMetaKeywords() { return $this->getField('meta_keywords'); }
	
		
	public function setContent1($p) { $this->setField('content1', $p); }
	public function getContent1() { return $this->getField('content1'); }
	
		
	public function setContent2($p) { $this->setField('content2', $p); }
	public function getContent2() { return $this->getField('content2'); }
	
		
	public function setCreated($p) { $this->setField('created', $p); }
	public function getCreated() { return $this->getField('created'); }
	
	
}

