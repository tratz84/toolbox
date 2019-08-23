<?php


namespace docqueue\model;


class DocumentDAO extends \core\db\DAOObject {

	public function __construct() {
		$this->setResource( 'default' );
		$this->setObjectName( '\\docqueue\\model\\Document' );
	}
	

	public function read($id) {
	    return $this->queryOne('select * from docqueue__document where document_id=?', array($id));
	}
	
	
	public function search($opts=array()) {
	    $qb = $this->createQueryBuilder();
	    
	    $qb->selectField('*');
	    $qb->setTable('docqueue__document');
	    
	    $qb->setOrderBy('document_id desc');
	    
	    return $qb->queryCursor(Document::class);
	}
	
}

