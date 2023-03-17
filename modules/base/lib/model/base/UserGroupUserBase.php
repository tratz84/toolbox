<?php


namespace base\model\base;


class UserGroupUserBase extends \core\db\DBObject {

	public function __construct($id=null) {
		$this->setResource( 'default' );
		$this->setTableName( 'base__user_group_user' );
		$this->setPrimaryKey( 'user_group_user_id' );
		$this->setDatabaseFields( array (
  'user_group_user_id' => 
  array (
    'Field' => 'user_group_user_id',
    'Type' => 'int',
    'Null' => 'NO',
    'Key' => 'PRI',
    'Default' => NULL,
    'Extra' => 'auto_increment',
  ),
  'user_group_id' => 
  array (
    'Field' => 'user_group_id',
    'Type' => 'int',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
  ),
  'user_id' => 
  array (
    'Field' => 'user_id',
    'Type' => 'int',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
  ),
) );
		
		if ($id != null)
			$this->setField($this->primaryKey, $id);
	}
	
		
	public function setUserGroupUserId($p) { $this->setField('user_group_user_id', $p); }
	public function getUserGroupUserId() { return $this->getField('user_group_user_id'); }
	
		
	public function setUserGroupId($p) { $this->setField('user_group_id', $p); }
	public function getUserGroupId() { return $this->getField('user_group_id'); }
	
		
	public function setUserId($p) { $this->setField('user_id', $p); }
	public function getUserId() { return $this->getField('user_id'); }
	
	
}

