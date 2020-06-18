<?php


namespace customer\model;


class CompanyTypeDAO extends \core\db\DAOObject {

	public function __construct() {
		$this->setResource( 'default' );
		$this->setObjectName( '\\customer\\model\\CompanyType' );
	}
	
	
	public function readAll() {
	    return $this->queryList('select * from customer__company_type order by sort');
	}

	
	public function read($id) {
	    $l = $this->queryList("select * from customer__company_type where company_type_id = ?", array($id));
	    
	    if (count($l)) {
	        return $l[0];
	    } else {
	        return null;
	    }
	}

	public function delete($id) {
	    $this->query("delete from customer__company_type where company_type_id = ?", array($id));
	}
	
}

