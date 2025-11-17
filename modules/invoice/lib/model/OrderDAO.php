<?php


namespace invoice\model;


class OrderDAO extends \core\db\DAOObject {

	public function __construct() {
		$this->setResource( 'default' );
		$this->setObjectName( '\\invoice\\model\\Order' );
	}
	
	
	
	public function orderStatusToNull( $orderStatusId ) {
	    $this->query('update invoice__order set order_status_id = null where order_status_id = ?', array($orderStatusId));
	}
	
	

}

