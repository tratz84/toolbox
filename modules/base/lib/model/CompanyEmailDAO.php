<?php


namespace base\model;


class CompanyEmailDAO extends \core\db\DAOObject {

	public function __construct() {
		$this->setResource( 'default' );
		$this->setObjectName( '\\base\\model\\CompanyEmail' );
	}
	

	
	public function insertOrUpdate($companyEmailId, $companyId, $emailId, $sort) {
	    
	    $ce = new CompanyEmail($companyEmailId);
	    if ($companyEmailId) $ce->read();
	    
	    $ce->setCompanyId($companyId);
	    $ce->setEmailId($emailId);
	    
	    return $ce->save();
	}
	
	public function readByCompany($companyId) {
	    return $this->queryList("select * from customer__company_email where company_id = ?", array($companyId));
	}
	
	
	public function delete($companyEmailId) {
	    $this->query("delete from customer__company_email where company_email_id = ?", array($companyEmailId));
	}
}

