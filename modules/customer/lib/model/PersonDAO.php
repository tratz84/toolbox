<?php


namespace customer\model;


use core\db\query\QueryBuilderWhere;

class PersonDAO extends \core\db\DAOObject {

	public function __construct() {
		$this->setResource( 'default' );
		$this->setObjectName( '\\customer\\model\\Person' );
	}
	
	
	public function readByCompany($companyId) {
	    $sql = "select p.*
                from customer__person p
                join customer__company_person cp ON (p.person_id = cp.person_id)
                where p.deleted = false and company_id = ?";
	    
	    return $this->queryList($sql, array($companyId));
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
	    
	    if (isset($opts['object_meta']) && is_array($opts['object_meta']) && count($opts['object_meta'])) {
	        foreach($opts['object_meta'] as $om) {
	            $subsql = "(select object_id from base__object_meta where object_name='".$this->escape(Person::class)."' and object_key='".$this->escape($om['object_key'])."' and object_value='".$this->escape($om['object_value'])."')";
	            $qb->addWhere(QueryBuilderWhere::whereRefByRef('person_id', 'IN', $subsql));
	        }
	    }
	    
	    
	    $qb->setOrderBy('lastname');
	    
	    return $qb->queryCursor(Person::class);
	}

	
	public function delete($id) {
// 	    return $this->query("delete from customer__person where person_id = ?", array($id));
        $this->query('update customer__person set deleted=true where person_id = ?', array($id));
	}
	
}

