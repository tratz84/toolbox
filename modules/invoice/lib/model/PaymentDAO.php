<?php


namespace invoice\model;


class PaymentDAO extends \core\db\DAOObject {

	public function __construct() {
		$this->setResource( 'default' );
		$this->setObjectName( '\\invoice\\model\\Payment' );
	}
	
	public function paymentMethodToNull($paymentMethodId) {
	    $this->query("update invoice__payment set payment_method_id = null where payment_method_id = ?", array($paymentMethodId));
	}
	
	
	public function read($id) {
	    return $this->queryOne("select * from invoice__payment where payment_id = ?", array($id));
	}
	
	public function delete($id) {
	    $this->query("delete from invoice__payment where payment_id = ?", array($id));
	}
	
	public function search($opts = array()) {
	    $where = array();
	    $params = array();
	    
	    $sql = "select p.*
                from invoice__payment p
                ";
	    
	    if (isset($opts['company_id']) && $opts['company_id'] > 0) {
	        $where[] = " p.company_id = ? ";
	        $params[] = $opts['company_id'];
	    }
	    
	    if (isset($opts['person_id']) && $opts['person_id'] > 0) {
	        $where[] = " p.person_id = ? ";
	        $params[] = $opts['person_id'];
	    }
	    
	    if (isset($opts['ref_object']) && $opts['ref_object']) {
	        $where[] = " p.ref_object = ? ";
	        $params[] = $opts['ref_object'];
	    }
	    
	    if (isset($opts['ref_id']) && $opts['ref_id'] > 0) {
	        $where[] = " p.ref_id = ? ";
	        $params[] = $opts['ref_id'];
	    }
	    
	    if (isset($opts['invoice_id']) && $opts['invoice_id'] > 0) {
	        $where[] = " p.invoice_id = ? ";
	        $params[] = $opts['invoice_id'];
	    }
	    
	    if (isset($opts['invoice_line_id']) && $opts['invoice_line_id'] > 0) {
	        $where[] = " p.invoice_line_id = ? ";
	        $params[] = $opts['invoice_line_id'];
	    }
	    
	    if (count($where)) {
	        $sql .= " WHERE (".implode(") AND (", $where) . ") ";
	    }
	    
	    $sql .= "
                order by payment_date desc, payment_id desc";
	    
	    return $this->queryCursor($sql, $params);
	}
	
	
	
	public function readTotalsForPeriod($start, $end, $refObject, $paymentType, $paymentMethodId) {
	    $sql = "select ref_id, sum(amount)
            	from invoice__payment
            	where payment_date >= ? and payment_date <= ? ";
	    
	    $where = array();
	    $params = array();
	    $params[] = $start;
	    $params[] = $end;
	    
	    if ($refObject) {
	        $where[] = " ref_object = ?";
	        $params[] = $refObject;
	    }
	    if ($paymentType) {
	        $where[] = " payment_type = ? ";
	        $params[] = $paymentType;
	    }
	    
	    if ($paymentMethodId) {
	        $where[] = " payment_method_id = ? ";
	        $params[] = $paymentMethodId;
	    }
	    
	    if (count($where)) {
	        $sql .= " AND ( " . implode(" ) AND ( ", $where) . ") ";
	    }

        $sql .= " group by ref_id";
	    
        $r = $this->query($sql, $params);
	    
	    $l = array();
	    
	    while ($row = $r->fetch_array()) {
	        $l[$row[0]] = $row[1];
	    }
	    
	    return $l;
	}
	

}

