<?php


namespace webmail\model\base;


class FilterConditionBase extends \core\db\DBObject {

	public function __construct($id=null) {
		$this->setResource( 'default' );
		$this->setTableName( 'webmail__filter_condition' );
		$this->setPrimaryKey( 'filter_condition_id' );
		$this->setDatabaseFields( array (
  'filter_condition_id' => 
  array (
    'Field' => 'filter_condition_id',
    'Type' => 'int(11)',
    'Null' => 'NO',
    'Key' => 'PRI',
    'Default' => NULL,
    'Extra' => 'auto_increment',
  ),
  'filter_id' => 
  array (
    'Field' => 'filter_id',
    'Type' => 'int(11)',
    'Null' => 'YES',
    'Key' => 'MUL',
    'Default' => NULL,
    'Extra' => '',
  ),
  'filter_field' => 
  array (
    'Field' => 'filter_field',
    'Type' => 'varchar(255)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
  ),
  'filter_type' => 
  array (
    'Field' => 'filter_type',
    'Type' => 'varchar(255)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
  ),
  'filter_pattern' => 
  array (
    'Field' => 'filter_pattern',
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
	
		
	public function setFilterConditionId($p) { $this->setField('filter_condition_id', $p); }
	public function getFilterConditionId() { return $this->getField('filter_condition_id'); }
	
		
	public function setFilterId($p) { $this->setField('filter_id', $p); }
	public function getFilterId() { return $this->getField('filter_id'); }
	
		
	public function setFilterField($p) { $this->setField('filter_field', $p); }
	public function getFilterField() { return $this->getField('filter_field'); }
	
		
	public function setFilterType($p) { $this->setField('filter_type', $p); }
	public function getFilterType() { return $this->getField('filter_type'); }
	
		
	public function setFilterPattern($p) { $this->setField('filter_pattern', $p); }
	public function getFilterPattern() { return $this->getField('filter_pattern'); }
	
		
	public function setSort($p) { $this->setField('sort', $p); }
	public function getSort() { return $this->getField('sort'); }
	
	
}

