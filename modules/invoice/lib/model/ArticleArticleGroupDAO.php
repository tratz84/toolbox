<?php


namespace invoice\model;


class ArticleArticleGroupDAO extends \core\db\DAOObject {

	public function __construct() {
		$this->setResource( 'default' );
		$this->setObjectName( '\\invoice\\model\\ArticleArticleGroup' );
	}
	

	public function deleteByArticleGroup($articleGroupId) {
	    $this->query("delete from article__article_group where article_group_id = ?", array($articleGroupId));
	}
	
}

