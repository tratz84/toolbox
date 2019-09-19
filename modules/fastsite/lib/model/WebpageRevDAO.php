<?php


namespace fastsite\model;


class WebpageRevDAO extends \core\db\DAOObject {

	public function __construct() {
		$this->setResource( 'default' );
		$this->setObjectName( '\\fastsite\\model\\WebpageRev' );
	}
	
	
	public function read($id) {
	    return $this->queryOne('select * from fastsite__webpage_rev where webpage_rev_id = ?', array($id));
	}
	
	public function delete($id) {
	    return $this->query('delete from fastsite__webpage_rev where webpage_rev_id = ?', array($id));
	}
	
	public function nextRevNo($webpageId) {
	    $sql = "select max(rev) from fastsite__webpage_rev where webpage_id = ?";
	    $v = (int)$this->queryValue($sql, array($webpageId));
	    
	    return $v + 1;
	}
	
}

