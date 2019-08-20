<?php


namespace fastsite\model\base;


class WebmenuBase extends \core\db\DBObject {

	public function __construct($id=null) {
		$this->setResource( 'default' );
		$this->setTableName( 'fastsite__webmenu' );
		$this->setPrimaryKey( 'webmenu_id' );
		$this->setDatabaseFields( array (
  'webmenu_id' => 
  array (
    'Field' => 'webmenu_id',
    'Type' => 'int(11)',
    'Null' => 'NO',
    'Key' => 'PRI',
    'Default' => NULL,
    'Extra' => 'auto_increment',
  ),
  'parent_webmenu_id' => 
  array (
    'Field' => 'parent_webmenu_id',
    'Type' => 'int(11)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
  ),
  'code' => 
  array (
    'Field' => 'code',
    'Type' => 'varchar(64)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
  ),
  'title' => 
  array (
    'Field' => 'title',
    'Type' => 'varchar(255)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
  ),
  'url' => 
  array (
    'Field' => 'url',
    'Type' => 'varchar(255)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
  ),
  'webpage_page_no' => 
  array (
    'Field' => 'webpage_page_no',
    'Type' => 'int(11)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
  ),
  'description' => 
  array (
    'Field' => 'description',
    'Type' => 'text',
    'Null' => 'YES',
    'Key' => '',
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
  'edited' => 
  array (
    'Field' => 'edited',
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
	
		
	public function setWebmenuId($p) { $this->setField('webmenu_id', $p); }
	public function getWebmenuId() { return $this->getField('webmenu_id'); }
	
		
	public function setParentWebmenuId($p) { $this->setField('parent_webmenu_id', $p); }
	public function getParentWebmenuId() { return $this->getField('parent_webmenu_id'); }
	
		
	public function setCode($p) { $this->setField('code', $p); }
	public function getCode() { return $this->getField('code'); }
	
		
	public function setTitle($p) { $this->setField('title', $p); }
	public function getTitle() { return $this->getField('title'); }
	
		
	public function setUrl($p) { $this->setField('url', $p); }
	public function getUrl() { return $this->getField('url'); }
	
		
	public function setWebpagePageNo($p) { $this->setField('webpage_page_no', $p); }
	public function getWebpagePageNo() { return $this->getField('webpage_page_no'); }
	
		
	public function setDescription($p) { $this->setField('description', $p); }
	public function getDescription() { return $this->getField('description'); }
	
		
	public function setSort($p) { $this->setField('sort', $p); }
	public function getSort() { return $this->getField('sort'); }
	
		
	public function setEdited($p) { $this->setField('edited', $p); }
	public function getEdited() { return $this->getField('edited'); }
	
		
	public function setCreated($p) { $this->setField('created', $p); }
	public function getCreated() { return $this->getField('created'); }
	
	
}

