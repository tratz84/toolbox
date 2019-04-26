<?php


namespace invoice\model;


use core\db\DatabaseHandler;

class PriceAdjustmentDAO extends \core\db\DAOObject {

	public function __construct() {
		$this->setResource( 'default' );
		$this->setObjectName( '\\invoice\\model\\PriceAdjustment' );
	}
	
	
	public function read($id) {
	    return $this->queryOne('select * from invoice__price_adjustment where price_adjustment_id = ?', array($id));
	}
	
	public function delete($id) {
        $this->query('delete from invoice__price_adjustment where price_adjustment_id = ?', array($id));
	}
	
	
	public function readByRef($refObject, $refId) {
	    return $this->queryList('select * from invoice__price_adjustment where ref_object = ? and ref_id = ? order by start_date asc', array($refObject, $refId));
	}
	
	public function deleteByRef($refObject, $refId) {
	    return $this->query('delete from invoice__price_adjustment where ref_object = ? and ref_id = ? ', array($refObject, $refId));
	}
	
	public function readByStart($refObject, $refId, $startDate) {
	    $sql = 'select * from invoice__price_adjustment where ref_object = ? and ref_id = ? and start_date = ?';
	    
	    return $this->queryOne($sql, array($refObject, $refId, $startDate));
	}
	
	public function readByRefPeildatum($refObject, $refId, $peildatum) {
	    $sql = 'select *
        	    from invoice__price_adjustment
        	    where ref_object=? and ref_id=? and start_date <= ?
    	        order by start_date desc
    	        limit 1';
	    
	    return $this->queryOne($sql, array($refObject, $refId, $peildatum));
	}
	
	public function search($opts) {
	    $sql = "select * 
                from invoice__price_adjustment pa ";
	    
	    $where = array();
	    $params = array();
	    
	    
	    if (isset($opts['refObject']) && $opts['refObject']) {
	        $where[] = "pa.ref_object = ?";
	        $params[] = $opts['refObject'];
	    }
	    
	    if (isset($opts['refId']) && $opts['refId']) {
	        $where[] = "pa.ref_id = ?";
	        $params[] = $opts['refId'];
	    }
	    
	    if (isset($opts['maxStartDate']) && $opts['maxStartDate']) {
	        $where[] = "pa.start_date <= ?";
	        $params[] = $opts['maxStartDate'];
	    }
	    
	    if (array_key_exists('executed', $opts)) {
	        $where[] = "pa.executed = " . ($opts['executed'] ? '1' : '0');
	    }
	    
	    if (count($where)) {
	        $sql .= ' WHERE (' . implode(") AND (", $where) . ") ";
	    }
	    
	    $sql .= " order by start_date asc, price_adjustment_id asc ";
	    
	    return $this->queryCursor($sql, $params);
	}
	
}

