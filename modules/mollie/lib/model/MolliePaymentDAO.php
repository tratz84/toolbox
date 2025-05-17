<?php


namespace mollie\model;


class MolliePaymentDAO extends \core\db\DAOObject {

	public function __construct() {
		$this->setResource( 'default' );
		$this->setObjectName( '\\mollie\\model\\MolliePayment' );
	}
	
	
	
	public function read( $id ) {
	    $sql = "select * from mollie__mollie_payment where mollie_payment_id = ?";
	    
	    return $this->queryOne( $sql, array($id) );
	}
	
	
	public function updateStatus( $id, $newStatus ) {
	    $sql = "update mollie__mollie_payment set mollie_status = ? where mollie_payment_id = ?";
	    
	    $this->query($sql, array($newStatus, $id));
	    
	    return $this->getAffectedRows();
	}

	
	public function search($opts=array()) {
	    
	    $qb = $this->createQueryBuilder();
	    $qb->setTable('mollie__mollie_payment')
	    ->selectFields('*');
	    
	    
	    
	    $qb->setOrderBy('created desc');
	    
	    return $qb->queryCursor(MolliePayment::class);
	}
	
	
	public function readOpenPayments( ) {
	    $sql = "select * from mollie__mollie_payment where mollie_status = ? and created >= ?";
	    
	    return $this->queryList( $sql, array($status, date('Y-m-d H:i:s', strtotime('-1 day'))) );
	}
	
	
	
}

