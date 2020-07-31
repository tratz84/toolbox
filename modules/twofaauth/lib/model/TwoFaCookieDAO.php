<?php


namespace twofaauth\model;


use core\db\query\QueryBuilderWhere;

class TwoFaCookieDAO extends \core\db\DAOObject {

	public function __construct() {
		$this->setResource( 'default' );
		$this->setObjectName( '\\twofaauth\\model\\TwoFaCookie' );
	}
	
	public function read($id) {
	    return $this->queryOne('select * from twofaauth__two_fa_cookie where cookie_id = ?', array($id));
	}
	
	public function readByValue( $val ) {
	    return $this->queryOne('select * from twofaauth__two_fa_cookie where cookie_value = ?', array($val));
	}
	
	public function updateLastVisit($cookieId) {
	    return $this->query('update twofaauth__two_fa_cookie 
                                set last_visit = ? 
                                where cookie_id = ?'
	               , array(date('Y-m-d H:i:s'), $cookieId));
	}
	
	
	
	public function search($opts) {
	    
	    $qb = $this->createQueryBuilder();
	    $qb->setTable('twofaauth__two_fa_cookie');
	    
	    
	    if (isset($opts['user_id']) && $opts['user_id']) {
	        $qb->addWhere(QueryBuilderWhere::whereRefByVal('user_id', '=', $opts['user_id']));
	    }
	    
	    if (isset($opts['after_created_date']) && $opts['after_created_date']) {
	        $qb->addWhere(QueryBuilderWhere::whereRefByVal('created', '>=', $opts['after_created_date']));
	    }
	    
	    if (isset($opts['limit']) && $opts['limit']) {
	       $qb->setLimit( 50 );
	    }
	    
	    
	    if (isset($opts['return_list']) && $opts['return_list']) {
    	    return $qb->queryList();
	    }
	    else {
	        return $qb->queryCursor();
	    }
	}

}


