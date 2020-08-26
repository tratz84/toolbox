<?php


namespace filesync\model;


use core\db\query\QueryBuilderWhere;

class WopiAccessDAO extends \core\db\DAOObject {

	public function __construct() {
		$this->setResource( 'default' );
		$this->setObjectName( '\\filesync\\model\\WopiAccess' );
	}
	
	
	
	public function read($id) {
	    return $this->queryOne('SELECT * FROM filesync__wopi_access where wopi_access_id = ?', array($id));
	}
	
	public function delete($id) {
	    return $this->query('DELETE FROM filesync__wopi_access where wopi_access_id = ?', array($id));
	}
	
	public function deleteAll() {
	    // note, TRUNCATE not used on purpose, because DELETE FROM doesn't reset AUTO_INCREMENT, which might be handy for support
	    return $this->query('DELETE FROM filesync__wopi_access');
	}
	
	public function cleanup() {
	    $sql = "delete
                from filesync__wopi_access
                where date_add(created, interval filesync__wopi_access.access_token_ttl second) < now() ";
	    
	    $this->query( $sql );
	}
	
	
	public function search($opts=array()) {
	    
	    $qb = $this->createQueryBuilder();
	    $qb->setTable('filesync__wopi_access');
	    $qb->leftJoin('base__user', 'user_id');
	    
	    $qb->selectField('*', 'filesync__wopi_access');
	    $qb->selectField('username', 'base__user');
	    
	    
	    if (isset($opts['access_token']) && trim($opts['access_token'])) {
	        $qb->addWhere( QueryBuilderWhere::whereRefByVal('access_token', 'LIKE', '%'.$opts['access_token'].'%') );
	    }
	    if (isset($opts['path']) && trim($opts['path'])) {
	        $qb->addWhere( QueryBuilderWhere::whereRefByVal('path', 'LIKE', '%'.$opts['path'].'%') );
	    }
	    if (isset($opts['username']) && trim($opts['username'])) {
	        $qb->addWhere( QueryBuilderWhere::whereRefByVal('username', 'LIKE', '%'.$opts['username'].'%') );
	    }
	    
	    
	    
	    return $qb->queryCursor();
	}
	

}

