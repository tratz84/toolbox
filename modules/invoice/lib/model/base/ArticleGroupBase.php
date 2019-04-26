<?php


namespace invoice\model\base;


class ArticleGroupBase extends \core\db\DBObject {

	public function __construct($id=null) {
		$this->setResource( 'default' );
		$this->setTableName( 'article__article_group' );
		$this->setPrimaryKey( 'article_group_id' );
		$this->setDatabaseFields( array (
  'article_group_id' => 
  array (
    'Field' => 'article_group_id',
    'Type' => 'int(11)',
    'Null' => 'NO',
    'Key' => 'PRI',
    'Default' => NULL,
    'Extra' => 'auto_increment',
  ),
  'parent_article_group_id' => 
  array (
    'Field' => 'parent_article_group_id',
    'Type' => 'int(11)',
    'Null' => 'YES',
    'Key' => 'MUL',
    'Default' => NULL,
    'Extra' => '',
  ),
  'group_name' => 
  array (
    'Field' => 'group_name',
    'Type' => 'varchar(255)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
  ),
  'long_description1' => 
  array (
    'Field' => 'long_description1',
    'Type' => 'mediumtext',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
  ),
  'long_description2' => 
  array (
    'Field' => 'long_description2',
    'Type' => 'mediumtext',
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
  'sort' => 
  array (
    'Field' => 'sort',
    'Type' => 'int(11)',
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
	
		
	public function setArticleGroupId($p) { $this->setField('article_group_id', $p); }
	public function getArticleGroupId() { return $this->getField('article_group_id'); }
	
		
	public function setParentArticleGroupId($p) { $this->setField('parent_article_group_id', $p); }
	public function getParentArticleGroupId() { return $this->getField('parent_article_group_id'); }
	
		
	public function setGroupName($p) { $this->setField('group_name', $p); }
	public function getGroupName() { return $this->getField('group_name'); }
	
		
	public function setLongDescription1($p) { $this->setField('long_description1', $p); }
	public function getLongDescription1() { return $this->getField('long_description1'); }
	
		
	public function setLongDescription2($p) { $this->setField('long_description2', $p); }
	public function getLongDescription2() { return $this->getField('long_description2'); }
	
		
	public function setActive($p) { $this->setField('active', $p); }
	public function getActive() { return $this->getField('active'); }
	
		
	public function setSort($p) { $this->setField('sort', $p); }
	public function getSort() { return $this->getField('sort'); }
	
		
	public function setEdited($p) { $this->setField('edited', $p); }
	public function getEdited() { return $this->getField('edited'); }
	
		
	public function setCreated($p) { $this->setField('created', $p); }
	public function getCreated() { return $this->getField('created'); }
	
	
}

