<?php


namespace invoice\model;


class InvoiceStatusDAO extends \core\db\DAOObject {

	public function __construct() {
		$this->setResource( 'default' );
		$this->setObjectName( '\\invoice\\model\\InvoiceStatus' );
	}
	

	public function read($id) {
	    return $this->queryOne("select * from invoice__invoice_status where invoice_status_id = ?", array($id));
	}

	public function delete($id) {
	    return $this->query("delete from invoice__invoice_status where invoice_status_id = ?", array($id));
	}
	
	public function readAll() {
	    return $this->queryList("select * from invoice__invoice_status order by sort");
	}
	
	public function readActive() {
	    return $this->queryList("select * from invoice__invoice_status where active = true order by sort");
	}
	
	
	public function readByDefaultStatus() {
	    return $this->queryOne("select * from invoice__invoice_status where default_selected = true");
	}
	
	
	public function readFirst() {
	    return $this->queryOne("select * from invoice__invoice_status order by sort limit 1");
	}
	
}

