<?php


namespace fastsite\model\base;


class WebpageBase extends \core\db\DBObject {

	public function __construct($id=null) {
		$this->setResource( 'default' );
		$this->setTableName( 'fastsite__webpage' );
		$this->setPrimaryKey( 'webpage_id' );
		$this->setDatabaseFields( array (
  'webpage_id' => 
  array (
    'Field' => 'webpage_id',
    'Type' => 'int(11)',
    'Null' => 'NO',
    'Key' => 'PRI',
    'Default' => NULL,
    'Extra' => 'auto_increment',
  ),
  'webpage_rev_id' => 
  array (
    'Field' => 'webpage_rev_id',
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
    'Key' => 'UNI',
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
  'fastsite_template_file' => 
  array (
    'Field' => 'fastsite_template_file',
    'Type' => 'varchar(255)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
  ),
  'module' => 
  array (
    'Field' => 'module',
    'Type' => 'varchar(64)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
  ),
  'active' => 
  array (
    'Field' => 'active',
    'Type' => 'tinyint(1)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => '1',
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
	
		
	public function setWebpageId($p) { $this->setField('webpage_id', $p); }
	public function getWebpageId() { return $this->getField('webpage_id'); }
	
		
	public function setWebpageRevId($p) { $this->setField('webpage_rev_id', $p); }
	public function getWebpageRevId() { return $this->getField('webpage_rev_id'); }
	
		
	public function setCode($p) { $this->setField('code', $p); }
	public function getCode() { return $this->getField('code'); }
	
		
	public function setUrl($p) { $this->setField('url', $p); }
	public function getUrl() { return $this->getField('url'); }
	
		
	public function setFastsiteTemplateFile($p) { $this->setField('fastsite_template_file', $p); }
	public function getFastsiteTemplateFile() { return $this->getField('fastsite_template_file'); }
	
		
	public function setModule($p) { $this->setField('module', $p); }
	public function getModule() { return $this->getField('module'); }
	
		
	public function setActive($p) { $this->setField('active', $p); }
	public function getActive() { return $this->getField('active'); }
	
		
	public function setEdited($p) { $this->setField('edited', $p); }
	public function getEdited() { return $this->getField('edited'); }
	
		
	public function setCreated($p) { $this->setField('created', $p); }
	public function getCreated() { return $this->getField('created'); }
	
	
}

