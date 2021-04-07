<?php


namespace webmail\model;


use core\db\query\QueryBuilderWhere;

class EmailDAO extends \core\db\DAOObject {

	public function __construct() {
		$this->setResource( 'default' );
		$this->setObjectName( '\\webmail\\model\\Email' );
	}
	
	
	public function search($opts) {
	    $qb = $this->createQueryBuilder();
	    
	    $qb->setTable('webmail__email');
	    $qb->selectField('*', 'webmail__email');
	    
	    if (ctx()->isModuleEnabled('customer')) {
    	    $qb->leftJoin('customer__company', 'company_id');
    	    $qb->selectField('company_name', 'customer__company');
    	    
    	    $qb->leftJoin('customer__person', 'person_id');
    	    $qb->selectField('person_id',       'customer__person');
    	    $qb->selectField('firstname',       'customer__person');
    	    $qb->selectField('insert_lastname', 'customer__person');
    	    $qb->selectField('lastname',        'customer__person');
    	    
    	    
    	    if (isset($opts['customer_name']) && $opts['customer_name']) {
    	        $nameFields = array();
    	        $nameFields[] = 'customer__person.firstname';
    	        $nameFields[] = 'customer__person.insert_lastname';
    	        $nameFields[] = 'customer__person.lastname';
    	        $nameFields[] = 'customer__person.insert_lastname';
    	        $nameFields[] = 'customer__person.firstname';
    	        $nameFields[] = 'customer__company.company_name';
    	        
    	        $str = '';
    	        foreach($nameFields as $f) {
    	            if ($str != '') {
    	                $str = $str . ', ';
    	            }
    	            $str .= " IFNULL({$f}, ''), ' ' ";
    	        }
	            
    	        $qb->addWhere( QueryBuilderWhere::whereRefByVal("concat( $str )", 'LIKE', '%'.$opts['customer_name'].'%') );
    	    }
	    }
	    
	    if (array_key_exists('incoming', $opts)) {
	        $qb->addWhere(QueryBuilderWhere::whereRefByVal('webmail__email.incoming', '=', ($opts['incoming'] ? 1 : 0)));
	    }
	    
	    if (isset($opts['from_name']) && $opts['from_name']) {
	        $qb->addWhere( QueryBuilderWhere::whereRefByVal('webmail__email.from_name', 'LIKE', '%'.$opts['from_name'].'%') );
	    }
	    
	    if (isset($opts['subject']) && $opts['subject']) {
	        $qb->addWhere( QueryBuilderWhere::whereRefByVal('webmail__email.subject', 'LIKE', '%'.$opts['subject'].'%') );
	    }
	    
	    if (isset($opts['status']) && $opts['status']) {
	        $qb->addWhere( QueryBuilderWhere::whereRefByVal('webmail__email.status', '=', $opts['status']) );
	    }
	    
	    if (isset($opts['orderby']) && $opts['orderby']) {
	        $qb->setOrderBy( $opts['orderby'] );
	    }
	    
	    return $qb->queryCursor();
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

