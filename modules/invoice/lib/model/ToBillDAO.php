<?php


namespace invoice\model;


class ToBillDAO extends \core\db\DAOObject {

	public function __construct() {
		$this->setResource( 'default' );
		$this->setObjectName( '\\invoice\\model\\ToBill' );
	}
	
	public function read($id) {
	    return $this->queryOne('select * from invoice__to_bill where to_bill_id = ?', array($id));
	}
	
	public function delete($id) {
	    $this->query('delete from invoice__to_bill where to_bill_id = ?', array($id));
	}
	
	public function search($opts) {
	    $sql = "select tb.*, c.company_name, p.firstname, p.insert_lastname, p.lastname
                from invoice__to_bill tb
                left join customer__company c using (company_id)
                left join customer__person p using (person_id) ";
	    
	    
	    $where = array();
	    $params = array();
	    
	    if (isset($opts['customer_name']) && trim($opts['customer_name'])) {
	        $where[] = " concat(p.lastname, ', ', p.insert_lastname, ' ', p.firstname) LIKE ? OR c.company_name LIKE ? ";
	        $params[] = '%'.$opts['customer_name'].'%';
	        $params[] = '%'.$opts['customer_name'].'%';
	    }
	    
	    if (isset($opts['short_description']) && $opts['short_description']) {
	        $where[] = 'tb.short_description LIKE ?';
	        $params[] = '%'.$opts['short_description'].'%';
	    }
	    
	    
	    if (isset($opts['paid']) && $opts['paid'] !== '') {
	        $where[] = 'tb.paid = ' . ($opts['paid']?'1':'0');
	    }
	    
	    
	    if (count($where)) {
	        $sql .= " WHERE (" . implode(") AND (", $where) . ")";
	    }
	    
	    $sql .= ' order by created desc';
	    
	    return $this->queryCursor($sql, $params);
	}

}

