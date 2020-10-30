<?php


namespace admin\model;


class UserCustomerDAO extends \core\db\DAOObject {

	public function __construct() {
		$this->setResource( 'admin' );
		$this->setObjectName( '\\admin\\model\\UserCustomer' );
	}
	
	
	
	public function readByUser($userId) {
	    $sql = "select uc.*, c.contextName
                from toolbox__user_customer uc
                join toolbox__customer c using  (customer_id)
                where user_id = ?";
	    
	    return $this->queryList($sql, array($userId));
	}
	
	public function deleteByUser($userId) {
	    return $this->query("delete from toolbox__user_customer where user_id = ?", array($userId));
	}

}

