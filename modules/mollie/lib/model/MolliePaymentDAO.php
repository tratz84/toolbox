<?php


namespace mollie\model;


class MolliePaymentDAO extends \core\db\DAOObject {

	public function __construct() {
		$this->setResource( 'default' );
		$this->setObjectName( '\\mollie\\model\\MolliePayment' );
	}
	
	
	
	public function readByMolliePaymentId( $id ) {
	    $sql = "select * from mollie__mollie_payment where mollie_payment_id = ?";
	    
	    return $this->queryOne( $sql, array($id) );
	}
	
	
	public function updateStatus( $id, $newStatus ) {
	    $sql = "update mollie__mollie_payment set status = ? where mollie_payment_id = ?";
	    
	    $this->query($sql, array($newStatus, $id));
	    
	    return $this->getAffectedRows();
	}

}

