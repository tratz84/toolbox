<?php


namespace invoice\model;


class OrderLineDAO extends \core\db\DAOObject {

	public function __construct() {
		$this->setResource( 'default' );
		$this->setObjectName( '\\invoice\\model\\OrderLine' );
	}
	
	
	
	
	public function deleteByOrder($id) {
	    $this->query("delete from invoice__order_line where order_id = ?", array($id));
	}
	
	
	public function readByOrder($id) {
	    $sql = "select * from invoice__order_line where order_id = ? order by sort";
	    
	    return $this->queryList($sql, array($id));
	}
	

}

