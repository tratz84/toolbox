<?php


namespace fail2ban\model;

use InvalidArgumentException;


class BanCheckDAO extends \core\db\DAOObject {

	public function __construct() {
		$this->setResource( 'default' );
		$this->setObjectName( '\\fail2ban\\model\\BanCheck' );
	}
	
	
	
	public function search( $opts ) {
	    
	    $qb = $this->createQueryBuilder();
	    
	    $qb->setTable('fail2ban__ban_check');
	    
	    $qb->selectField('ban_check_id');
	    $qb->selectField('ip');
	    $qb->selectField('message');
	    $qb->selectField('created');
	    
	    $qb->setOrderBy('ban_check_id desc');
	    
	    return $qb->queryCursor( BanCheck::class );
	}

	
	
	
	public function abuseCount( $ip, $created ) {
	    $sql = "select count(*)
                from fail2ban__ban_check
                where ip = ?
                    and created >= ?";
	    
	    return $this->queryValue( $sql, array($ip, $created) );
	}
	
	
	public function abuseCountLike( $ipSearch, $created ) {
	    
	    $sql = 'select count(*)
                        from fail2ban__ban_check
                        where created >= ?
                            and ip LIKE ?';
	    
	    return $this->queryValue( $sql, array( $created, $ipSearch ) );
	}
	
	
	public function deleteByFilter( $f ) {
	    
	    $params = array();
	    $sql = "delete from fail2ban__ban_check ";
	    
	    
	    if ($f && $f != '%') {
	        $params[] = $f;
	        $sql .= ' where ip like ? ';
	    }
	    
	    $this->query( $sql, $params );
	}
}

