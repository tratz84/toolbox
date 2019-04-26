<?php


namespace invoice\model;


class InvoiceLineDAO extends \core\db\DAOObject {

	public function __construct() {
		$this->setResource( 'default' );
		$this->setObjectName( '\\invoice\\model\\InvoiceLine' );
	}
	
	
	public function readByInvoice($invoiceId) {
	    return $this->queryList("select * from invoice__invoice_line where invoice_id = ? order by sort", array($invoiceId));
	}
	
	public function deleteByInvoice($invoiceId) {
	    return $this->query("delete from invoice__invoice_line where invoice_id = ?", array($invoiceId));
	}
	
}

