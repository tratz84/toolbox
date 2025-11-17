<?php


namespace invoice\model;


class OrderStatusDAO extends \core\db\DAOObject {

	public function __construct() {
		$this->setResource( 'default' );
		$this->setObjectName( '\\invoice\\model\\OrderStatus' );
	}
	
	
	public function read($id) {
	    return $this->queryOne('select * from invoice__order_status where order_status_id = ?', array($id));
	}
	
	
	public function readAll() {
	    return $this->queryList('select * from invoice__order_status order by sort');
	}
	
	public function readActive() {
	    return $this->queryList('select * from invoice__order_status where active = true order by sort');
	}
	
	
	public function delete($id) {
	    $this->query('delete from invoice__order_status where order_status_id = ?', array($id));
	}
	
	
	
	public function readByDefaultStatus() {
	    return $this->queryOne("select * from invoice__order_status where default_selected = true");
	}
	
	
	public function readFirst() {
	    return $this->queryOne("select * from invoice__order_status order by sort limit 1");
	}
	

}

