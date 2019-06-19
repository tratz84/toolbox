<?php


namespace invoice\model;


class VatDAO extends \core\db\DAOObject {

	public function __construct() {
		$this->setResource( 'default' );
		$this->setObjectName( '\\invoice\\model\\Vat' );
	}
	

	
	public function readAll() {
	    return $this->queryList("select * from invoice__vat order by sort");
	}
	
	public function readActive() {
	    return $this->queryList("select * from invoice__vat where visible = true order by sort");
	}
	
	public function readDefault() {
	    $qb = $this->createQueryBuilder();
	    $qb->selectFields('*');
	    $qb->setTable('invoice__vat');
	    $qb->setLimit(1);
	    $qb->setOrderBy('default_selected desc, sort');
	    
	    return $qb->queryOne( Vat::class );
	}
	
	public function read($id) {
	    $sql = "select * from invoice__vat where vat_id = ?";
	    
	    $l = $this->queryList($sql, array($id));
	    
	    if (count($l)) {
	        return $l[0];
	    } else {
	        return null;
	    }
	}
	
	public function unsetDefaultSelected($except_vatId) {
	    $this->query("update invoice__vat set default_selected = false where vat_id <> ?", array($except_vatId));
	}
	
	
	public function delete($id) {
	    $this->query("delete from invoice__vat where vat_id = ?", array($id));
	}
	
	
	public function readByArticle($articleId) {
	    return $this->queryOne("select v.*
                            from invoice__vat v
                            left join article__article a using (vat_id)
                            where a.article_id = ?", array($articleId));
	}
	
}

