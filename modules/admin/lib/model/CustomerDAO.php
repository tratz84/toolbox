<?php


namespace admin\model;


class CustomerDAO extends \core\db\DAOObject {

	public function __construct() {
		$this->setResource( 'admin' );
		$this->setObjectName( '\\admin\\model\\Customer' );
	}
	
	public function readAll() {
	    return $this->queryList("select * from toolbox__customer order by contextName");
	}
	
	public function readCustomers($ids) {
	    $intIds = array();
	    foreach($ids as $i) {
	        if (intval($i)) {
	            $intIds[] = intval($i);
	        }
	    }
	    
	    if (count($intIds) == 0)
	        return array();
	    
        return $this->queryList("select * from toolbox__customer where customer_id IN (".implode(', ', $intIds).") order by contextName");
	}
	

	public function readByName($n) {
	    $l = $this->queryList("select * from toolbox__customer where contextName = ?", array($n));
	    
	    if (count($l))
	        return $l[0];
	    else
	       return null;
	}
	
	public function updateDescription( $customerId, $description ) {
	    $this->query('update toolbox__customer set description = ? where customer_id = ?', array( $description, $customerId ));
	}
	
}

