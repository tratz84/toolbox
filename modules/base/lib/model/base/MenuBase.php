<?php


namespace base\model\base;


class MenuBase extends \core\db\DBObject {

	public function __construct($id=null) {
		$this->setResource( 'default' );
		$this->setTableName( 'base__menu' );
		$this->setPrimaryKey( 'menu_id' );
		$this->setDatabaseFields( array (
  'menu_id' => 
  array (
    'Field' => 'menu_id',
    'Type' => 'int(11)',
    'Null' => 'NO',
    'Key' => 'PRI',
    'Default' => NULL,
    'Extra' => 'auto_increment',
  ),
  'menu_code' => 
  array (
    'Field' => 'menu_code',
    'Type' => 'varchar(64)',
    'Null' => 'YES',
    'Key' => 'UNI',
    'Default' => NULL,
    'Extra' => '',
  ),
  'parent_menu_code' => 
  array (
    'Field' => 'parent_menu_code',
    'Type' => 'varchar(64)',
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
  'visible' => 
  array (
    'Field' => 'visible',
    'Type' => 'tinyint(1)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => '1',
    'Extra' => '',
  ),
) );
		
		if ($id != null)
			$this->setField($this->primaryKey, $id);
	}
	
		
	public function setMenuId($p) { $this->setField('menu_id', $p); }
	public function getMenuId() { return $this->getField('menu_id'); }
	
		
	public function setMenuCode($p) { $this->setField('menu_code', $p); }
	public function getMenuCode() { return $this->getField('menu_code'); }
	
		
	public function setParentMenuCode($p) { $this->setField('parent_menu_code', $p); }
	public function getParentMenuCode() { return $this->getField('parent_menu_code'); }
	
		
	public function setSort($p) { $this->setField('sort', $p); }
	public function getSort() { return $this->getField('sort'); }
	
		
	public function setVisible($p) { $this->setField('visible', $p); }
	public function getVisible() { return $this->getField('visible'); }
	
	
}

