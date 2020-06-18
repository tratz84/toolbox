<?php


namespace customer\model;


class CompanyAddressDAO extends \core\db\DAOObject {

	public function __construct() {
		$this->setResource( 'default' );
		$this->setObjectName( '\\customer\\model\\CompanyAddress' );
	}
	
	
	public function insertOrUpdate($companyAddressId, $companyId, $addressId, $sort) {
	    
	    $ca = new CompanyAddress($companyAddressId);
	    if ($companyAddressId) $ca->read();
	    
	    $ca->setCompanyId($companyId);
	    $ca->setAddressId($addressId);
	    
	    return $ca->save();
	}
	
	
	public function delete($companyAddressId) {
	    if (!$companyAddressId)
	        return false;
	    
	    $this->query("delete from customer__company_address where company_address_id = ?", array($companyAddressId));
	}

	public function readByCompany($companyId) {
	    return $this->queryList("select * from customer__company_address where company_id = ?", array($companyId));
	}
	
}

