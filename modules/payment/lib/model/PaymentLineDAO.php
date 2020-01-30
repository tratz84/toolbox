<?php


namespace payment\model;


class PaymentLineDAO extends \core\db\DAOObject {

	public function __construct() {
		$this->setResource( 'default' );
		$this->setObjectName( '\\payment\\model\\PaymentLine' );
	}
	
	public function deleteByPayment($paymentId) {
	    $sql = "delete from payment__payment_line where payment_id = ?";
	    
	    return $this->query($sql, array($paymentId));
	}
	
	public function readByPayment($paymentId) {
	    $sql = "select * from payment__payment_line where payment_id = ? order by sort";
	    
	    return $this->queryList($sql, array($paymentId));
	}

}

