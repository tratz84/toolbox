<?php


namespace base\model;



class AddressDAO extends \core\db\DAOObject {

	public function __construct() {
		$this->setResource( 'default' );
		$this->setObjectName( '\\base\\model\\Address' );
	}
	

	public function readByCompany($company_id) {
	    $sql = "select *
                from customer__address a
                join customer__company_address ca on (a.address_id = ca.address_id)
                where ca.company_id=?
                order by ca.sort ";
	    
	    return $this->queryList($sql, array($company_id));
	}
	
	public function readByPerson($person_id) {
	    $sql = "select *
                from customer__address a
                join customer__person_address ca on (a.address_id = ca.address_id)
                where ca.person_id=?
                order by ca.sort ";
	    
	    return $this->queryList($sql, array($person_id));
	}
	
	public function saveForCompany($companyId, $list) {
	    $caDao = new CompanyAddressDAO();
	    
	    $addressIds = array();
	    
	    for($x=0; $x < count($list); $x++) {
	        $l = $list[$x];
	        
	        if ($l->save()) {
    	        $addressIds[] = $l->getAddressId();
    	        $caDao->insertOrUpdate($l->getField('company_address_id'), $companyId, $l->getField('address_id'), $x);
	        }
	    }
	    
	    $sql = "delete from customer__address where address_id in (select address_id from customer__company_address where company_id = ?) ";
	    if (count($addressIds))
	        $sql .= " and address_id not in (".implode(',', $addressIds).") ";
	    
	        
	    $this->query($sql, array($companyId));
	}
	
	
	public function delete($addressId) {
	    $this->query("delete from customer__address where address_id = ?", array($addressId));
	}
	
}

