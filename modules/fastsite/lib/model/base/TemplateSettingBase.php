<?php


namespace fastsite\model\base;


class TemplateSettingBase extends \core\db\DBObject {

	public function __construct($id=null) {
		$this->setResource( 'default' );
		$this->setTableName( 'fastsite__template_setting' );
		$this->setPrimaryKey( 'template_setting_id' );
		$this->setDatabaseFields( array (
  'template_setting_id' => 
  array (
    'Field' => 'template_setting_id',
    'Type' => 'int(11)',
    'Null' => 'NO',
    'Key' => 'PRI',
    'Default' => NULL,
    'Extra' => 'auto_increment',
  ),
  'template_name' => 
  array (
    'Field' => 'template_name',
    'Type' => 'varchar(255)',
    'Null' => 'YES',
    'Key' => 'UNI',
    'Default' => NULL,
    'Extra' => '',
  ),
  'active' => 
  array (
    'Field' => 'active',
    'Type' => 'tinyint(1)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => '0',
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
	
		
	public function setTemplateSettingId($p) { $this->setField('template_setting_id', $p); }
	public function getTemplateSettingId() { return $this->getField('template_setting_id'); }
	
		
	public function setTemplateName($p) { $this->setField('template_name', $p); }
	public function getTemplateName() { return $this->getField('template_name'); }
	
		
	public function setActive($p) { $this->setField('active', $p); }
	public function getActive() { return $this->getField('active'); }
	
		
	public function setEdited($p) { $this->setField('edited', $p); }
	public function getEdited() { return $this->getField('edited'); }
	
		
	public function setCreated($p) { $this->setField('created', $p); }
	public function getCreated() { return $this->getField('created'); }
	
	
}

