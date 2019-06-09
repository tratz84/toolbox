<?php


namespace base\model;


use core\db\query\QueryBuilderWhere;

class PersonDAO extends \core\db\DAOObject {

	public function __construct() {
		$this->setResource( 'default' );
		$this->setObjectName( '\\base\\model\\Person' );
	}
	
	
	
	public function search($opts=array()) {
	    
	    $qb = $this->createQueryBuilder();
	    $qb->setTable('customer__person')
	       ->selectFields('*');
	    
	    
	    $qb->addWhere(QueryBuilderWhere::whereRefByVal('deleted', '=', false));
	    
	    if (isset($opts['customername']) && trim($opts['customername']) != '') {
	        $qb->addWhere(QueryBuilderWhere::whereRefByVal("concat(firstname, ' ', insert_lastname, ' ', lastname)", 'LIKE', '%'.$opts['customername'].'%'));
	    }
	    
	    if (isset($opts['firstname']) && trim($opts['firstname']) != '') {
	        $qb->addWhere(QueryBuilderWhere::whereRefByVal('firstname', 'LIKE', '%'.$opts['firstname'].'%'));
	    }
	    
	    if (isset($opts['lastname']) && trim($opts['lastname']) != '') {
	        $qb->addWhere(QueryBuilderWhere::whereRefByVal('lastname', 'LIKE', '%'.$opts['lastname'].'%'));
	    }
	    
	    $qb->setOrderBy('lastname');
	    
	    return $qb->queryCursor($this);
	}

	
	public function delete($id) {
// 	    return $this->query("delete from customer__person where person_id = ?", array($id));
        $this->query('update customer__person set deleted=true where person_id = ?', array($id));
	}
	
}

