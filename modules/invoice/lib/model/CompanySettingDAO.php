<?php


namespace invoice\model;


class CompanySettingDAO extends \core\db\DAOObject {

	public function __construct() {
		$this->setResource( 'default' );
		$this->setObjectName( '\\invoice\\model\\CompanySetting' );
	}
	
	
	public function read($id) {
	    return $this->queryOne("select * from invoice__company_setting where company_setting_id = ?", array($id));
	}

	public function readByCompany($companyId) {
	    return $this->queryOne("select * from invoice__company_setting where company_id = ?", array($companyId));
	}
	
	public function hasTaxExcemption($companyId) {
	    $sql = "select tax_excemption
                from invoice__company_setting
                where company_id=?";
	    
	    return $this->queryValue($sql, array($companyId)) ? true : false;
	}
	
}

