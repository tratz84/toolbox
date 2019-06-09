<?php


namespace base\model;


use core\db\query\QueryBuilderWhere;

class CompanyDAO extends \core\db\DAOObject {

	public function __construct() {
		$this->setResource( 'default' );
		$this->setObjectName( '\\base\\model\\Company' );
	}
	
	
	public function search($opts=array()) {
	    $qb = $this->createQueryBuilder();
	    
	    $qb->selectField('*');
	    $qb->setTable('customer__company');
	    
	    $qb->addWhere(QueryBuilderWhere::whereRefByVal('customer__company.deleted', '=', false));
	    
	    if (isset($opts['company_name']) && trim($opts['company_name']) != '') {
	        $qb->addWhere(QueryBuilderWhere::whereRefByVal('company_name', 'LIKE', '%'.$opts['company_name'].'%'));
	    }

	    if (isset($opts['contact_person']) && trim($opts['contact_person']) != '') {
	        $qb->addWhere(QueryBuilderWhere::whereRefByVal('contact_person', 'LIKE', '%'.$opts['contact_person'].'%'));
	    }
	    
	    $qb->setOrderBy('company_name');
	    
	    return $qb->queryCursor($this);
	}

	
	
	public function delete($id) {
	    $qb = $this->createQueryBuilder();
	    $qb->setTable('customer__company');
	    $qb->setFieldValue('deleted', true);
	    $qb->addWhere(QueryBuilderWhere::whereRefByVal('company_id', '=', $id));
	    
	    return $qb->queryUpdate();
	}
	
	
	
	public function setCompanyTypeToNULL($companyTypeId) {
	    $qb = $this->createQueryBuilder();
	    $qb->setTable('customer__company');
	    $qb->setFieldValue('company_type_id', null);
	    $qb->addWhere(QueryBuilderWhere::whereRefByVal('company_type_id', '=', $companyTypeId));
	    
	    $qb->queryUpdate();
	}
	
}

