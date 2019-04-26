<?php


namespace base\model;


class PhoneDAO extends \core\db\DAOObject {

	public function __construct() {
		$this->setResource( 'default' );
		$this->setObjectName( '\\base\\model\\Phone' );
	}
	
	
	public function readByCompany($id) {
	    $sql = "select *
                from customer__phone p
                join customer__company_phone cp on (cp.phone_id = p.phone_id)
                where cp.company_id = ? 
                order by cp.sort ";
	    
	    return $this->queryList($sql, array($id));
	}

	
	public function readByPerson($id) {
	    $sql = "select *
                from customer__phone p
                join customer__person_phone cp on (cp.phone_id = p.phone_id)
                where cp.person_id = ? 
                order by cp.sort ";
	    
	    return $this->queryList($sql, array($id));
	}
	
	
	public function saveForCompany($companyId, $list) {
	    $cpDao = new CompanyPhoneDAO();
	    
	    $phoneIds = array();
	    
	    
	    for($x=0; $x < count($list); $x++) {
	        $l = $list[$x];
	        
	        if ($l->save()) {
	            $phoneIds[] = $l->getPhoneId();
	            $cpDao->insertOrUpdate($l->getField('company_phone_id'), $companyId, $l->getField('phone_id'), $x);
	        }
	    }
	    
	    $sql = "delete from customer__phone where phone_id in (select phone_id from customer__company_phone where company_id = ?) ";
	    if (count($phoneIds))
	        $sql .= " and phone_id not in (".implode(',', $phoneIds).") ";
	        
        $this->query($sql, array($companyId));
	}
	
	
	public function delete($phoneId) {
	    $this->query("delete from customer__phone where phone_id = ?", array($phoneId));
	}
	
}

