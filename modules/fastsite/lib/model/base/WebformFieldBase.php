<?php


namespace fastsite\model\base;


class WebformFieldBase extends \core\db\DBObject {

	public function __construct($id=null) {
		$this->setResource( 'default' );
		$this->setTableName( 'fastsite__webform_field' );
		$this->setPrimaryKey( 'webform_field_id' );
		$this->setDatabaseFields( array (
  'webform_field_id' => 
  array (
    'Field' => 'webform_field_id',
    'Type' => 'int(11)',
    'Null' => 'NO',
    'Key' => 'PRI',
    'Default' => NULL,
    'Extra' => 'auto_increment',
  ),
  'webform_id' => 
  array (
    'Field' => 'webform_id',
    'Type' => 'int(11)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
  ),
  'input_field' => 
  array (
    'Field' => 'input_field',
    'Type' => 'varchar(255)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
  ),
  'input_options' => 
  array (
    'Field' => 'input_options',
    'Type' => 'text',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
  ),
  'label' => 
  array (
    'Field' => 'label',
    'Type' => 'varchar(255)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
  ),
  'default_value' => 
  array (
    'Field' => 'default_value',
    'Type' => 'varchar(255)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
  ),
  'validator' => 
  array (
    'Field' => 'validator',
    'Type' => 'varchar(255)',
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
) );
		
		if ($id != null)
			$this->setField($this->primaryKey, $id);
	}
	
		
	public function setWebformFieldId($p) { $this->setField('webform_field_id', $p); }
	public function getWebformFieldId() { return $this->getField('webform_field_id'); }
	
		
	public function setWebformId($p) { $this->setField('webform_id', $p); }
	public function getWebformId() { return $this->getField('webform_id'); }
	
		
	public function setInputField($p) { $this->setField('input_field', $p); }
	public function getInputField() { return $this->getField('input_field'); }
	
		
	public function setInputOptions($p) { $this->setField('input_options', $p); }
	public function getInputOptions() { return $this->getField('input_options'); }
	
		
	public function setLabel($p) { $this->setField('label', $p); }
	public function getLabel() { return $this->getField('label'); }
	
		
	public function setDefaultValue($p) { $this->setField('default_value', $p); }
	public function getDefaultValue() { return $this->getField('default_value'); }
	
		
	public function setValidator($p) { $this->setField('validator', $p); }
	public function getValidator() { return $this->getField('validator'); }
	
		
	public function setSort($p) { $this->setField('sort', $p); }
	public function getSort() { return $this->getField('sort'); }
	
	
}

