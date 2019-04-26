<?php


namespace base\model\base;


class SettingBase extends \core\db\DBObject {

	public function __construct($id=null) {
		$this->setResource( 'default' );
		$this->setTableName( 'base__setting' );
		$this->setPrimaryKey( 'setting_id' );
		$this->setDatabaseFields( array (
  'setting_id' => 
  array (
    'Field' => 'setting_id',
    'Type' => 'int(11)',
    'Null' => 'NO',
    'Key' => 'PRI',
    'Default' => NULL,
    'Extra' => 'auto_increment',
  ),
  'setting_type' => 
  array (
    'Field' => 'setting_type',
    'Type' => 'varchar(16)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
  ),
  'setting_code' => 
  array (
    'Field' => 'setting_code',
    'Type' => 'varchar(64)',
    'Null' => 'YES',
    'Key' => 'UNI',
    'Default' => NULL,
    'Extra' => '',
  ),
  'short_description' => 
  array (
    'Field' => 'short_description',
    'Type' => 'longtext',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
  ),
  'long_description' => 
  array (
    'Field' => 'long_description',
    'Type' => 'longtext',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
  ),
  'text_value' => 
  array (
    'Field' => 'text_value',
    'Type' => 'longtext',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
  ),
) );
		
		if ($id != null)
			$this->setField($this->primaryKey, $id);
	}
	
		
	public function setSettingId($p) { $this->setField('setting_id', $p); }
	public function getSettingId() { return $this->getField('setting_id'); }
	
		
	public function setSettingType($p) { $this->setField('setting_type', $p); }
	public function getSettingType() { return $this->getField('setting_type'); }
	
		
	public function setSettingCode($p) { $this->setField('setting_code', $p); }
	public function getSettingCode() { return $this->getField('setting_code'); }
	
		
	public function setShortDescription($p) { $this->setField('short_description', $p); }
	public function getShortDescription() { return $this->getField('short_description'); }
	
		
	public function setLongDescription($p) { $this->setField('long_description', $p); }
	public function getLongDescription() { return $this->getField('long_description'); }
	
		
	public function setTextValue($p) { $this->setField('text_value', $p); }
	public function getTextValue() { return $this->getField('text_value'); }
	
	
}

