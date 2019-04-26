<?php


namespace base\model;


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
	    $sql = "select a.*, c.company_name, p.firstname, p.insert_lastname, p.lastname
                from base__activity a
                left join customer__company c using (company_id)
                left join customer__person p using (person_id)
                order by activity_id desc limit 100";
	    
	    return $this->queryList($sql);
	}
	
	public function read($id) {
	    $sql = "select a.*, c.company_name, p.firstname, p.insert_lastname, p.lastname
                from base__activity a
                left join customer__company c using (company_id)
                left join customer__person p using (person_id)
                where a.activity_id = ?";
	    
	    $l = $this->queryList($sql, array($id));
	    
	    if (count($l)) {
	        return $l[0];
	    } else {
	        return null;
	    }
	}
	
}

