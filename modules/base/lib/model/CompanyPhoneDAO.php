<?php


namespace base\model;


class CompanyPhoneDAO extends \core\db\DAOObject {

	public function __construct() {
		$this->setResource( 'default' );
		$this->setObjectName( '\\base\\model\\CompanyPhone' );
	}
	
	
	
	public function insertOrUpdate($companyPhoneId, $companyId, $phoneId) {
	    
	    $cp = new CompanyPhone($companyPhoneId);
	    if ($companyPhoneId) $cp->read();
	    
	    $cp->setCompanyId($companyId);
	    $cp->setPhoneId($phoneId);
	    
	    return $cp->save();
	}

	
	public function readByCompany($companyId) {
	    return $this->queryList("select * from customer__company_phone where company_id = ?", array($companyId));
	}
	
	public function delete($companyPhoneId) {
	    $this->query("delete from customer__company_phone where company_phone_id = ?", array($companyPhoneId));
	}
}

