<?php


namespace fastsite\model;


class WebpageDAO extends \core\db\DAOObject {

	public function __construct() {
		$this->setResource( 'default' );
		$this->setObjectName( '\\fastsite\\model\\Webpage' );
	}
	

	public function search($opts=array()) {
	    
	    $qb = $this->createQueryBuilder();
	    
	    $qb->setTable('fastsite__webpage');
	    
	    $qb->setOrderBy('url');
	    
	    $sql = $qb->createSelect();
	    
	    return $this->queryCursor($sql);
	}
	
	
}

