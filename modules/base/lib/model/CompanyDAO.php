<?php


namespace base\model;


class CompanyDAO extends \core\db\DAOObject {

	public function __construct() {
		$this->setResource( 'default' );
		$this->setObjectName( '\\base\\model\\Company' );
	}
	
	
	public function search($opts=array()) {
	    
	    $sql = "select * from customer__company ";
	    
	    $where = array();
	    $params = array();
	    
	    $where[] = " customer__company.deleted = false ";
	    
	    if (isset($opts['company_name']) && trim($opts['company_name']) != '') {
	        $where[] = "company_name LIKE ? ";
	        $params[] = '%'.$opts['company_name'].'%';
	    }

	    if (isset($opts['contact_person']) && trim($opts['contact_person']) != '') {
	        $where[] = "contact_person LIKE ? ";
	        $params[] = '%'.$opts['contact_person'].'%';
	    }
	    
	    if (count($where)) {
	        $sql .= "where ( " . implode(") AND (", $where) . ") ";
	    }
	    
	    $sql .= 'order by company_name';
	    
	    return $this->queryCursor($sql, $params);
	}

	
	
	public function delete($id) {
	    return $this->query("update customer__company set deleted = true where company_id = ?", array($id));
	}
	
	
	
	public function setCompanyTypeToNULL($companyTypeId) {
	    $this->query("update customer__company set company_type_id = NULL where company_type_id = ?", array($companyTypeId));
	}
	
}

