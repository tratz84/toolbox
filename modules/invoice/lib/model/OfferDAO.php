<?php


namespace invoice\model;


use core\Context;

class OfferDAO extends \core\db\DAOObject {

	public function __construct() {
		$this->setResource( 'default' );
		$this->setObjectName( '\\invoice\\model\\Offer' );
	}
	

	public function offerStatusToNull($id) {
	    $this->query("update invoice__offer set offer_status_id = null where offer_status_id = ?", array($id));
	}
	
	
	public function updateStatus($offerId, $offerStatusId) {
	    $this->query('update invoice__offer set offer_status_id = ? where offer_id = ?', array($offerStatusId, $offerId));
	}
	
	
	public function search($opts = array()) {
	    $where = array();
	    $params = array();
	    
	    $sql = "select o.*, o.total_calculated_price, o.total_calculated_price_incl_vat, os.description offer_status_description, c.company_name, p.firstname, p.insert_lastname, p.lastname
                from invoice__offer o
                left join invoice__offer_status os using (offer_status_id)
                left join customer__company c using (company_id)
                left join customer__person p using (person_id) ";

	    if (isset($opts['offerNumberText']) && trim($opts['offerNumberText'])) {
	        $prefix = Context::getInstance()->getPrefixNumbers();
	        
	        $nr = $opts['offerNumberText'];
	        
	        if (stripos($opts['offerNumberText'], $prefix) === 0)
	            $nr = substr($nr, strlen($prefix));
	        
            $where[] = " o.offer_number LIKE ? ";
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
	    
	    if (isset($opts['offer_status_description']) && is_numeric($opts['offer_status_description'])) {
	        $where[] = " o.offer_status_id = ? ";
	        $params[] = $opts['offer_status_description'];
	    }
	    
	    if (isset($opts['customer_name']) && trim($opts['customer_name'])) {
	        $where[] = " concat(p.lastname, ', ', p.insert_lastname, ' ', p.firstname) LIKE ? OR c.company_name LIKE ? ";
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
                from invoice__offer
                where offer_id = ?";
	    
	    $l = $this->queryList($sql, array($id));
	    if (count($l)) {
	        return $l[0];
	    } else {
	        return null;
	    }
	}
	
	public function generateOfferNumber() {
	    $offerPrefixCounter = date('Y') . '-';
	    
	    $offers = $this->queryList("select * from invoice__offer where offer_number like ?", array($offerPrefixCounter.'%'));
	    $max = 1;
	    foreach($offers as $o) {
	        if (strpos($o->getOfferNumber(), $offerPrefixCounter) !== 0) continue;
	        
	        $pos = (int)str_replace($offerPrefixCounter, '', $o->getOfferNumber());
	        
	        if ($pos >= $max) {
	            $max = $pos + 1;
	        }
	    }
	    
	    return $offerPrefixCounter . $max;
	}
	
	
	public function delete($id) {
	    $this->query("delete from invoice__offer where offer_id = ?", array($id));
	}
	
	
}

