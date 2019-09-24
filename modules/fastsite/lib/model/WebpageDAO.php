<?php


namespace fastsite\model;


class WebpageDAO extends \core\db\DAOObject {

	public function __construct() {
		$this->setResource( 'default' );
		$this->setObjectName( '\\fastsite\\model\\Webpage' );
	}
	
	public function readAll() {
	    return $this->queryList('select * from fastsite__webpage');
	}
	
	public function read($id) {
	    return $this->queryOne('select * from fastsite__webpage where webpage_id = ?', array($id));
	}
	
	
	public function readByCode($code) {
	    return $this->queryOne('select * from fastsite__webpage where code = ?', array($code));
	}
	
	public function readByUrl($url) {
	    return $this->queryOne('select * from fastsite__webpage where url = ?', array($url));
	}
	
	
	public function updateWebpageRev($webpageId, $webpageRevId) {
	    $this->query('update fastsite__webpage set webpage_rev_id = ? where webpage_id = ?', array($webpageRevId, $webpageId));
	}
	
	
	public function search($opts=array()) {
	    
	    $qb = $this->createQueryBuilder();
	    
	    $qb->setTable('fastsite__webpage');
	    $qb->join('fastsite__webpage_rev', 'webpage_rev_id');
	    
	    $qb->setOrderBy('url');
	    
	    $sql = $qb->createSelect();
	    
	    return $this->queryCursor($sql);
	}
	
	public function readRevByUrl($url) {
	    $sql = "select w.*, r.*
                from fastsite__webpage w
                left join fastsite__webpage_rev r on (w.webpage_rev_id = w.webpage_rev_id)
                where w.url = ?";
	    
	    return $this->queryList($sql, array($url));
	}
	
	
}

