<?php


namespace invoice\model;


class ArticleGroupDAO extends \core\db\DAOObject {

	public function __construct() {
		$this->setResource( 'default' );
		$this->setObjectName( '\\invoice\\model\\ArticleGroup' );
	}
	
	
	
	public function cursorAll() {
	    return $this->queryCursor("select * from article__article_group order by group_name");
	}

	
	public function read($id) {
	    $l = $this->queryList('select * from article__article_group where article_group_id = ?', array($id));
	    
	    if (count($l))
	        return $l[0];
	    else
	        return null;
	}
	
	public function deleteByArticleGroup($articleGroupId) {
	    $this->query("delete from article__article_group where article_group_id = ?", array($articleGroupId));
	}
	
}

