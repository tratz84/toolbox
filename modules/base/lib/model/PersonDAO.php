<?php


namespace base\model;


class PersonDAO extends \core\db\DAOObject {

	public function __construct() {
		$this->setResource( 'default' );
		$this->setObjectName( '\\base\\model\\Person' );
	}
	
	
	
	public function search($opts=array()) {
	    
	    $sql = "select * from customer__person ";
	    
	    $where = array();
	    $params = array();
	    
	    $where[] = "customer__person.deleted = false";
	    
	    if (isset($opts['customername']) && trim($opts['customername']) != '') {
	        $where[] = " concat(firstname, ' ', insert_lastname, ' ', lastname) LIKE ? ";
	        $params[] = '%'.$opts['customername'].'%';
	    }
	    
	    if (isset($opts['firstname']) && trim($opts['firstname']) != '') {
	        $where[] = " firstname LIKE ? ";
	        $params[] = '%'.$opts['firstname'].'%';
	    }
	    
	    if (isset($opts['lastname']) && trim($opts['lastname']) != '') {
	        $where[] = " lastname LIKE ? ";
	        $params[] = '%'.$opts['lastname'].'%';
	    }
	    
	    if (count($where)) {
	        $sql .= "where ( " . implode(") AND (", $where) . ") ";
	    }
	    
	    $sql .= 'order by lastname';
	    
	    return $this->queryCursor($sql, $params);
	}

	
	public function delete($id) {
// 	    return $this->query("delete from customer__person where person_id = ?", array($id));
        $this->query('update customer__person set deleted=true where person_id = ?', array($id));
	}
	
}

