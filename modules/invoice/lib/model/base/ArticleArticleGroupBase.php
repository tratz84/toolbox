<?php


namespace invoice\model\base;


class ArticleArticleGroupBase extends \core\db\DBObject {

	public function __construct($id=null) {
		$this->setResource( 'default' );
		$this->setTableName( 'article__article_article_group' );
		$this->setPrimaryKey( '' );
		$this->setDatabaseFields( array (
  'article_group_id' => 
  array (
    'Field' => 'article_group_id',
    'Type' => 'int(11)',
    'Null' => 'NO',
    'Key' => 'PRI',
    'Default' => NULL,
    'Extra' => '',
  ),
  'article_id' => 
  array (
    'Field' => 'article_id',
    'Type' => 'int(11)',
    'Null' => 'NO',
    'Key' => 'PRI',
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
	
		
	public function setArticleGroupId($p) { $this->setField('article_group_id', $p); }
	public function getArticleGroupId() { return $this->getField('article_group_id'); }
	
		
	public function setArticleId($p) { $this->setField('article_id', $p); }
	public function getArticleId() { return $this->getField('article_id'); }
	
		
	public function setSort($p) { $this->setField('sort', $p); }
	public function getSort() { return $this->getField('sort'); }
	
	
}

