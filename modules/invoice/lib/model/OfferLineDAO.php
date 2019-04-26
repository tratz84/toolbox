<?php


namespace invoice\model;


class OfferLineDAO extends \core\db\DAOObject {

	public function __construct() {
		$this->setResource( 'default' );
		$this->setObjectName( '\\invoice\\model\\OfferLine' );
	}
	
	
	public function deleteByOffer($id) {
	    $this->query("delete from invoice__offer_line where offer_id = ?", array($id));
	}
	
	
	public function readByOffer($id) {
	    $sql = "select * from invoice__offer_line where offer_id = ? order by sort";
	    
	    return $this->queryList($sql, array($id));
	}

}

