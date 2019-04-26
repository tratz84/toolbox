<?php


namespace webmail\model;


class EmailDAO extends \core\db\DAOObject {

	public function __construct() {
		$this->setResource( 'default' );
		$this->setObjectName( '\\webmail\\model\\Email' );
	}
	
	
	public function search($opts) {
	    $sql = "select e.*, c.company_name, p.firstname, p.insert_lastname, p.lastname
                from webmail__email e
                left join customer__company c using (company_id)
                left join customer__person p using (person_id) ";
	    
	    $where = array();
	    $params = array();
	    
	    if (array_key_exists('incoming', $opts)) {
	        $where[] = " e.incoming = " . ($opts['incoming'] ? 1 : 0);
	    }
	    
	    
	    if (count($where)) {
	        $sql .= " WHERE (" . implode(") AND (", $where) . ")";
	    }
	    
	    if (isset($opts['orderby'])) {
	        if (preg_match('/^[a-zA-Z_ ]+$/', $opts['orderby'])) {
	            $sql .= ' order by '.$opts['orderby'];
	        }
	    }
	    
	    
	    return $this->queryCursor($sql, $params);
	}
	
	public function read($id) {
	    $l = $this->queryList("select * from webmail__email where email_id = ?", array($id));
	    
	    if (count($l)) {
	        return $l[0];
	    } else {
	        return null;
	    }
	}
	
	
	public function markAsSent($id) {
	    $this->query("update webmail__email set status='sent' where email_id = ?", array($id));
	}
	
	
	public function readReceived($searchId, $messageId, $receivedDate) {
	    return $this->queryOne('select * from webmail__email where search_id = ? and message_id = ? and received = ?', array($searchId, $messageId, $receivedDate));
	}
	
	

}

