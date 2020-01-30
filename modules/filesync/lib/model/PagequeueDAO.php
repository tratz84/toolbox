<?php


namespace filesync\model;


class PagequeueDAO extends \core\db\DAOObject {

	public function __construct() {
		$this->setResource( 'default' );
		$this->setObjectName( '\\filesync\\model\\Pagequeue' );
	}
	

	
	public function read($id) {
	    return $this->queryOne('select * from filesync__pagequeue where pagequeue_id=?', array($id));
	}
	
	
	public function search($opts=array()) {
	    $qb = $this->createQueryBuilder();
	    
	    $qb->selectField('*');
	    $qb->setTable('filesync__pagequeue');
	    
	    $qb->setOrderBy('pagequeue_id desc');
	    
	    return $qb->queryCursor(Pagequeue::class);
	}
	
	
}

