<?php


namespace webmail\model\base;


class FilterActionBase extends \core\db\DBObject {

	public function __construct($id=null) {
		$this->setResource( 'default' );
		$this->setTableName( 'webmail__filter_action' );
		$this->setPrimaryKey( 'filter_action_id' );
		$this->setDatabaseFields( array (
  'filter_action_id' => 
  array (
    'Field' => 'filter_action_id',
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
  'filter_action' => 
  array (
    'Field' => 'filter_action',
    'Type' => 'varchar(255)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
  ),
  'filter_action_property' => 
  array (
    'Field' => 'filter_action_property',
    'Type' => 'varchar(255)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
  ),
  'filter_action_value' => 
  array (
    'Field' => 'filter_action_value',
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
	
		
	public function setFilterActionId($p) { $this->setField('filter_action_id', $p); }
	public function getFilterActionId() { return $this->getField('filter_action_id'); }
	
		
	public function setFilterId($p) { $this->setField('filter_id', $p); }
	public function getFilterId() { return $this->getField('filter_id'); }
	
		
	public function setFilterAction($p) { $this->setField('filter_action', $p); }
	public function getFilterAction() { return $this->getField('filter_action'); }
	
		
	public function setFilterActionProperty($p) { $this->setField('filter_action_property', $p); }
	public function getFilterActionProperty() { return $this->getField('filter_action_property'); }
	
		
	public function setFilterActionValue($p) { $this->setField('filter_action_value', $p); }
	public function getFilterActionValue() { return $this->getField('filter_action_value'); }
	
		
	public function setSort($p) { $this->setField('sort', $p); }
	public function getSort() { return $this->getField('sort'); }
	
	
}

