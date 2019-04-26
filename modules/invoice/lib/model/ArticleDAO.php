<?php


namespace invoice\model;


class ArticleDAO extends \core\db\DAOObject {

	public function __construct() {
		$this->setResource( 'default' );
		$this->setObjectName( '\\invoice\\model\\Article' );
	}
	
	public function read($id) {
	    $l = $this->queryList("select * from article__article where article_id = ?", array($id));
	    
	    if (count($l)) {
	        return $l[0];
	    } else {
	        return null;
	    }
	}
	
	public function searchCursor($opts) {
	    $sql = "select a.*, v.percentage vat_percentage, v.description vat_description
                from article__article a 
                left join invoice__vat v using (vat_id) ";
	    
	    $where = array();
	    $params = array();
	    
	    
	    if (isset($opts['active'])) {
	        $where[] = " a.active = " . ($opts['active'] ? '1' : '0');
	    }

	    if (isset($opts['article_name_exact']) && trim($opts['article_name_exact']) != '') {
	        $where[] = " a.article_name = ? ";
	        $params[] = $opts['article_name_exact'];
	    }
	    
	    if (isset($opts['article_name']) && trim($opts['article_name']) != '') {
	        $where[] = " a.article_name LIKE ? ";
	        $params[] = $opts['article_name'].'%';
	    }
	    
	    if (isset($opts['article_type']) && is_string($opts['article_type']) && trim($opts['article_type']) != '') {
	        $where[] = " a.article_type = ? ";
	        $params[] = $opts['article_type'];
	    }
	    
	    if (isset($opts['article_type']) && is_array($opts['article_type']) && count($opts['article_type'])) {
	        $tmp = '';
	        foreach($opts['article_type'] as $at) {
	            if ($tmp != '') $tmp = $tmp . ' OR ';
	            $tmp .= " a.article_type = ? ";
	            $params[] = $at;
	        }
	        
	        $where[] = '('.$tmp.')';
	    }
	    
	    if (count($where)) {
	        $sql .= " WHERE (" . implode(") AND (", $where) . ") ";
	    }
	    $sql .= "ORDER BY a.article_name";
	    
	    return $this->queryCursor($sql, $params);
	}
	

	public function delete($articleId) {
	    $this->query("delete from article__article where article_id = ?", array($articleId));
	}
	
	
	public function markDeleted($articleId) {
	    $this->query('update article__article set deleted=true where article_id = ?', array($articleId));
	}
	
}

