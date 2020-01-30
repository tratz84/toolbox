<?php


namespace payment\model;


class PaymentDAO extends \core\db\DAOObject {

	public function __construct() {
		$this->setResource( 'default' );
		$this->setObjectName( '\\payment\\model\\Payment' );
	}
	
	
	public function read($id) {
	    return $this->queryOne('select * from payment__payment where payment_id = ?', array($id));
	}
	
	public function delete($id) {
	    return $this->query('delete from payment__payment where payment_id = ?', array($id));
	}
	

}

