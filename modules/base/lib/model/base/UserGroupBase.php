<?php


namespace base\model\base;


class UserGroupBase extends \core\db\DBObject {

	public function __construct($id=null) {
		$this->setResource( 'default' );
		$this->setTableName( 'base__user_group' );
		$this->setPrimaryKey( 'user_group_id' );
		$this->setDatabaseFields( array (
  'user_group_id' => 
  array (
    'Field' => 'user_group_id',
    'Type' => 'int',
    'Null' => 'NO',
    'Key' => 'PRI',
    'Default' => NULL,
    'Extra' => 'auto_increment',
  ),
  'parent_user_group_id' => 
  array (
    'Field' => 'parent_user_group_id',
    'Type' => 'int',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
  ),
  'group_name' => 
  array (
    'Field' => 'group_name',
    'Type' => 'varchar(128)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
  ),
  'sort' => 
  array (
    'Field' => 'sort',
    'Type' => 'int',
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
	
		
	public function setUserGroupId($p) { $this->setField('user_group_id', $p); }
	public function getUserGroupId() { return $this->getField('user_group_id'); }
	
		
	public function setParentUserGroupId($p) { $this->setField('parent_user_group_id', $p); }
	public function getParentUserGroupId() { return $this->getField('parent_user_group_id'); }
	
		
	public function setGroupName($p) { $this->setField('group_name', $p); }
	public function getGroupName() { return $this->getField('group_name'); }
	
		
	public function setSort($p) { $this->setField('sort', $p); }
	public function getSort() { return $this->getField('sort'); }
	
		
	public function setEdited($p) { $this->setField('edited', $p); }
	public function getEdited() { return $this->getField('edited'); }
	
		
	public function setCreated($p) { $this->setField('created', $p); }
	public function getCreated() { return $this->getField('created'); }
	
	
}

