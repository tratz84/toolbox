<?php


namespace base\model;


use core\db\QueryBuilderWhere;

class ActivityDAO extends \core\db\DAOObject {

	public function __construct() {
		$this->setResource( 'default' );
		$this->setObjectName( '\\base\\model\\Activity' );
	}
	
	public function search($opts = array()) {
	    $where = array();
	    $params = array();
	    
	    $sql = "select a.* , c.company_name, p.firstname, p.insert_lastname, p.lastname
                from base__activity a
                left join customer__company c using (company_id) 
                left join customer__person p using (person_id)
                ";
	    
	    if (isset($opts['company_id']) && $opts['company_id'] > 0) {
	        $where[] = " a.company_id = ? ";
	        $params[] = $opts['company_id'];
	    }
	    
	    if (isset($opts['person_id']) && $opts['person_id'] > 0) {
	        $where[] = " a.person_id = ? ";
	        $params[] = $opts['person_id'];
	    }
	    
	    if (isset($opts['username']) && trim($opts['username'])) {
	        $where[] = " a.username = ? ";
	        $params[] = $opts['username'];
	    }
	    
	    if (isset($opts['customer_name']) && trim($opts['customer_name'])) {
	        $where[] = " c.company_name LIKE ? OR concat(p.firstname, ' ', p.insert_lastname, ' ', p.lastname) LIKE ? ";
	        $params[] = '%'.$opts['customer_name'].'%';
	        $params[] = '%'.$opts['customer_name'].'%';
	    }
	    
	    if (isset($opts['ref_object']) && trim($opts['ref_object'])) {
	        $where[] = " a.ref_object LIKE  ? ";
	        $params[] = '%'.$opts['ref_object'];
	    }
	    
	    if (isset($opts['ref_id']) && trim($opts['ref_id'])) {
	        $where[] = " a.ref_id = ? ";
	        $params[] = $opts['ref_id'];
	    }
	    
	    if (isset($opts['short_description']) && trim($opts['short_description'])) {
	        $where[] = " a.short_description LIKE ? ";
	        $params[] = '%'.$opts['short_description'].'%';
	    }
	    
	    
	    if (count($where)) {
	        $sql .= " WHERE (".implode(") AND (", $where) . ") ";
	    }
	    
	    $sql .= "
                order by activity_id desc";
	    
	    return $this->queryCursor($sql, $params);
	}
	
	public function readLatest() {
	    $qb = $this->createQueryBuilder();
	    $qb->selectFields('base__activity.*', 'customer__company.company_name', 'customer__person.firstname', 'customer__person.insert_lastname', 'customer__person.lastname');
	    $qb->setTable('base__activity');
	    $qb->leftJoin('customer__company', 'company_id');
	    $qb->leftJoin('customer__person', 'person_id');
	    $qb->setOrderBy('activity_id desc');
	    $qb->setLimit(100);
	    
	    return $qb->queryList($this);
	}
	
	public function read($id) {
	    $qb = $this->createQueryBuilder();
	    $qb->selectFields('base__activity.*', 'customer__company.company_name', 'customer__person.firstname', 'customer__person.insert_lastname', 'customer__person.lastname');
	    $qb->setTable('base__activity');
	    $qb->leftJoin('customer__company', 'company_id');
	    $qb->leftJoin('customer__person', 'person_id');
	    $qb->addWhere(QueryBuilderWhere::whereRefByVal('base__activity.activity_id', '=', $id));
	    
	    $l = $qb->queryList($this);
	    
	    if (count($l)) {
	        return $l[0];
	    } else {
	        return null;
	    }
	}
	
}

