<?php


namespace webmail\model\base;


class FilterBase extends \core\db\DBObject {

	public function __construct($id=null) {
		$this->setResource( 'default' );
		$this->setTableName( 'webmail__filter' );
		$this->setPrimaryKey( 'filter_id' );
		$this->setDatabaseFields( array (
  'filter_id' => 
  array (
    'Field' => 'filter_id',
    'Type' => 'int(11)',
    'Null' => 'NO',
    'Key' => 'PRI',
    'Default' => NULL,
    'Extra' => 'auto_increment',
  ),
  'connector_id' => 
  array (
    'Field' => 'connector_id',
    'Type' => 'int(11)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
  ),
  'filter_name' => 
  array (
    'Field' => 'filter_name',
    'Type' => 'varchar(255)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
  ),
  'match_method' => 
  array (
    'Field' => 'match_method',
    'Type' => 'enum(\'match_all\',\'match_one\')',
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
  'active' => 
  array (
    'Field' => 'active',
    'Type' => 'tinyint(1)',
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
	
		
	public function setFilterId($p) { $this->setField('filter_id', $p); }
	public function getFilterId() { return $this->getField('filter_id'); }
	
		
	public function setConnectorId($p) { $this->setField('connector_id', $p); }
	public function getConnectorId() { return $this->getField('connector_id'); }
	
		
	public function setFilterName($p) { $this->setField('filter_name', $p); }
	public function getFilterName() { return $this->getField('filter_name'); }
	
		
	public function setMatchMethod($p) { $this->setField('match_method', $p); }
	public function getMatchMethod() { return $this->getField('match_method'); }
	
		
	public function setSort($p) { $this->setField('sort', $p); }
	public function getSort() { return $this->getField('sort'); }
	
		
	public function setActive($p) { $this->setField('active', $p); }
	public function getActive() { return $this->getField('active'); }
	
		
	public function setEdited($p) { $this->setField('edited', $p); }
	public function getEdited() { return $this->getField('edited'); }
	
		
	public function setCreated($p) { $this->setField('created', $p); }
	public function getCreated() { return $this->getField('created'); }
	
	
}

