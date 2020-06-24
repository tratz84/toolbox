<?php


namespace base\model;


use core\db\query\QueryBuilderWhere;
use core\db\query\QueryBuilderWhereContainer;

class UserDAO extends \core\db\DAOObject {

	public function __construct() {
		$this->setResource( 'default' );
		$this->setObjectName( '\\base\\model\\User' );
	}
	
	public function delete($userId) {
	    $this->query('delete from base__user where user_id = ?', array($userId));
	}
	
	public function readAll() {
	    $qb = $this->createQueryBuilder();
	    $qb->setTable('base__user');
	    
	    return $qb->queryList( User::class );
	}
	
	public function search($opts=array()) {
	    
	    $qb = $this->createQueryBuilder();
	    $qb->setTable('base__user');
	    
	    if (isset($opts['name']) && trim($opts['name']) != '') {
	        $q1 = QueryBuilderWhere::whereRefByVal('username', 'LIKE', '%'.$opts['name'].'%');
	        $q2 = QueryBuilderWhere::whereRefByVal("concat(firstname, ' ', lastname, ' ', firstname)"
	            , 'LIKE', '%'.$opts['name'].'%');
	        
	        $qbwc = new QueryBuilderWhereContainer();
	        $qbwc->setJoinMethod('OR');
	        $qbwc->addWhere( $q1 );
	        $qbwc->addWhere( $q2 );
	        
	        $qb->addWhere( $qbwc );
	    }
	    
	    
	    if (isset($opts['username']) && trim($opts['username']) != '') {
	        $qb->addWhere(QueryBuilderWhere::whereRefByVal('username', 'LIKE', '%'.$opts['username'].'%'));
	    }
	    
	    if (isset($opts['email']) && trim($opts['email']) != '') {
	        $qb->addWhere(QueryBuilderWhere::whereRefByVal('email', 'LIKE', '%'.$opts['email'].'%'));
	    }
	    
	    if (isset($opts['user_type']) && trim($opts['user_type']) != '') {
	        $qb->addWhere(QueryBuilderWhere::whereRefByVal('user_type', 'LIKE', '%'.$opts['user_type'].'%'));
	    }
	    
	    $qb->setOrderBy('username');
	    
	    return $qb->queryCursor( User::class );
	}
	
	
	public function read($id) {
	    $qb = $this->createQueryBuilder()
	               ->setTable('base__user')
	               ->addWhere(QueryBuilderWhere::whereRefByVal('user_id', '=', $id));
	    
	    return $qb->queryOne( User::class );
	}

	public function readByUsername($u) {
	    $l = $this->queryList("select * from base__user where username = ?", array($u));
	    
	    if (count($l)) {
	        return $l[0];
	    } else {
	        return null;
	    }
	}
	
	public function setPassword($userId, $pw) {
	    return $this->query('update base__user set password = ? where user_id = ?', array($pw, $userId));
	}
	
	
	public function setAutologinToken($user_id, $token) {
	    $this->query("update base__user set autologin_token = ? where user_id = ?", array($token, $user_id));
	}
	
	
	public function readByAutologinToken($token) {
	    return $this->queryList("select * from base__user where autologin_token = ?", array($token));
	}
	
	public function resetAutologinToken($token) {
	    $this->query("update base__user set autologin_token = null where autologin_token = ?", array($token));
	}

}

