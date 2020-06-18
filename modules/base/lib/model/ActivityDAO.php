<?php


namespace base\model;


use core\db\query\QueryBuilderWhere;
use core\db\query\QueryBuilderWhereContainer;

class ActivityDAO extends \core\db\DAOObject {

	public function __construct() {
		$this->setResource( 'default' );
		$this->setObjectName( '\\base\\model\\Activity' );
	}
	
	public function search($opts = array()) {
	    $qb = $this->createQueryBuilder();
	    
	    
	    $qb->selectFields('base__activity.*');
	    $qb->setTable('base__activity');

	    // customer-module fields
	    if (ctx()->isModuleEnabled('customer')) {
	        $qb->selectFields('customer__company.company_name', 'customer__person.firstname', 'customer__person.insert_lastname', 'customer__person.lastname');
	        
	        $qb->leftJoin('customer__company', 'company_id');
	        $qb->leftJoin('customer__person', 'person_id');
    	    
    	    if (isset($opts['company_id']) && $opts['company_id'] > 0) {
    	        $qb->addWhere(QueryBuilderWhere::whereRefByVal('base__activity.company_id', '=', $opts['company_id']));
    	    }
    	    
    	    if (isset($opts['person_id']) && $opts['person_id'] > 0) {
    	        $qb->addWhere(QueryBuilderWhere::whereRefByVal('base__activity.person_id', '=', $opts['person_id']));
    	    }
    	    if (isset($opts['customer_name']) && trim($opts['customer_name'])) {
    	        $qbwc = new QueryBuilderWhereContainer('OR');
    	        $qbwc->addWhere(QueryBuilderWhere::whereRefByVal('customer__company.company_name', 'LIKE', '%'.$opts['customer_name'].'%'));
    	        $qbwc->addWhere(QueryBuilderWhere::whereRefByVal("concat(customer__person.firstname, ' ', customer__person.insert_lastname, ' ', customer__person.lastname)", 'LIKE', '%'.$opts['customer_name'].'%'));
    	        
    	        $qb->addWhere($qbwc);
    	    }
	    }
	    
	    if (isset($opts['username']) && trim($opts['username'])) {
	        $qb->addWhere(QueryBuilderWhere::whereRefByVal('base__activity.username', '=', $opts['username']));
	    }
	    
	    
	    if (isset($opts['ref_object']) && trim($opts['ref_object'])) {
	        $qb->addWhere(QueryBuilderWhere::whereRefByVal('base__activity.ref_object', 'LIKE', '%'.addslashes($opts['ref_object'])));
	    }
	    
	    if (isset($opts['ref_id']) && trim($opts['ref_id'])) {
	        $qb->addWhere(QueryBuilderWhere::whereRefByVal('base__activity.ref_id', '=', $opts['ref_id']));
	    }
	    
	    if (isset($opts['short_description']) && trim($opts['short_description'])) {
	        $qb->addWhere(QueryBuilderWhere::whereRefByVal('base__activity.short_description', 'LIKE', '%'.$opts['short_description'].'%'));
	    }
	    
	    $qb->setOrderBy('activity_id desc');
	    
	    return $qb->queryCursor( Activity::class );
	}
	
	public function readLatest() {
	    $qb = $this->createQueryBuilder();
	    $qb->selectFields('base__activity.*');
	    $qb->setTable('base__activity');
	    if (ctx()->isModuleEnabled('customer')) {
	        $qb->selectFields('customer__company.company_name', 'customer__person.firstname', 'customer__person.insert_lastname', 'customer__person.lastname');
	        $qb->leftJoin('customer__company', 'company_id');
    	    $qb->leftJoin('customer__person', 'person_id');
	    }
	    $qb->setOrderBy('activity_id desc');
	    $qb->setLimit(100);
	    
	    return $qb->queryList(Activity::class);
	}
	
	public function read($id) {
	    $qb = $this->createQueryBuilder();
	    $qb->selectFields('base__activity.*');
	    $qb->setTable('base__activity');
	    if (ctx()->isModuleEnabled('customer')) {
	        $qb->selectFields('customer__company.company_name', 'customer__person.firstname', 'customer__person.insert_lastname', 'customer__person.lastname');
	        $qb->leftJoin('customer__company', 'company_id');
	        $qb->leftJoin('customer__person', 'person_id');
	    }
	    $qb->addWhere(QueryBuilderWhere::whereRefByVal('base__activity.activity_id', '=', $id));
	    
	    $a = $qb->queryOne(Activity::class);
	    
	    return $a;
	}
	
}

