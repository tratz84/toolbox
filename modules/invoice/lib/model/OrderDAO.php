<?php


namespace invoice\model;

use core\Context;


class OrderDAO extends \core\db\DAOObject {

	public function __construct() {
		$this->setResource( 'default' );
		$this->setObjectName( '\\invoice\\model\\Order' );
	}
	
	
	
	public function orderStatusToNull( $orderStatusId ) {
	    $this->query('update invoice__order set order_status_id = null where order_status_id = ?', array($orderStatusId));
	}
	
	
	
	public function updateStatus($orderId, $orderStatusId) {
	    $this->query('update invoice__order set order_status_id = ? where order_id = ?', array($orderStatusId, $orderId));
	}
	
	
	public function search($opts = array()) {
	    $where = array();
	    $params = array();
	    
	    $sql = "select o.*, o.total_calculated_price, o.total_calculated_price_incl_vat, os.description order_status_description, c.company_name, p.firstname, p.insert_lastname, p.lastname
                from invoice__order o
                left join invoice__order_status os using (order_status_id)
                left join customer__company c using (company_id)
                left join customer__person p using (person_id) ";
	    
	    if (isset($opts['orderNumberText']) && trim($opts['orderNumberText'])) {
	        $prefix = Context::getInstance()->getPrefixNumbers();
	        
	        $nr = $opts['orderNumberText'];
	        
	        if (stripos($opts['orderNumberText'], $prefix) === 0)
	            $nr = substr($nr, strlen($prefix));
	            
	            $where[] = " o.order_number LIKE ? ";
	            $params[] = '%'.$nr;
	    }
	    
	    if (isset($opts['company_id'])) {
	        $where[] = " o.company_id = ? ";
	        $params[] = $opts['company_id'];
	    }
	    
	    if (isset($opts['person_id'])) {
	        $where[] = " o.person_id = ? ";
	        $params[] = $opts['person_id'];
	    }
	    
	    if (isset($opts['order_status_description']) && is_numeric($opts['order_status_description'])) {
	        $where[] = " o.order_status_id = ? ";
	        $params[] = $opts['order_status_description'];
	    }
	    
	    if (isset($opts['customer_name']) && trim($opts['customer_name'])) {
	        $where[] = " concat_ws('', p.lastname, ', ', p.insert_lastname, ' ', p.firstname) LIKE ? OR c.company_name LIKE ? ";
	        $params[] = '%'.$opts['customer_name'].'%';
	        $params[] = '%'.$opts['customer_name'].'%';
	    }
	    if (isset($opts['subject']) && trim($opts['subject'])) {
	        $where[] = ' o.subject LIKE ? ';
	        $params[] = '%'.$opts['subject'].'%';
	    }
	    
	    if (count($where)) {
	        $sql .= " WHERE (".implode(") AND (", $where) . ") ";
	    }
	    
	    
	    if (isset($opts['order'])) {
	        $order = filterOrderBy($opts['order']);
	    } else {
	        $order = ' created desc';
	    }
	    
	    $sql .= "
                order by " . $order;
	    
	    return $this->queryCursor($sql, $params);
	}
	
	
	public function read($id) {
	    $sql = "select *
                from invoice__order
                where order_id = ?";
	    
	    $l = $this->queryList($sql, array($id));
	    if (count($l)) {
	        return $l[0];
	    } else {
	        return null;
	    }
	}
	
	public function generateOrderNumber() {
	    $orderPrefixCounter = date('Y') . '-';
	    
	    $orders = $this->queryList("select * from invoice__order where order_number like ?", array($orderPrefixCounter.'%'));
	    $max = 1;
	    foreach($orders as $o) {
	        if (strpos($o->getOrderNumber(), $orderPrefixCounter) !== 0) continue;
	        
	        $pos = (int)str_replace($orderPrefixCounter, '', $o->getOrderNumber());
	        
	        if ($pos >= $max) {
	            $max = $pos + 1;
	        }
	    }
	    
	    return $orderPrefixCounter . $max;
	}
	
	
	public function delete($id) {
	    $this->query("delete from invoice__order where order_id = ?", array($id));
	}
	
	

}

