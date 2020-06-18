<?php


namespace customer\model;


class CountryDAO extends \core\db\DAOObject {

	public function __construct() {
		$this->setResource( 'default' );
		$this->setObjectName( '\\customer\\model\\Country' );
	}
	
	public function read($countryId) {
	    return $this->queryOne('select * from customer__country where country_id = ?', array($countryId));
	}
	
	public function readAll() {
	    $l = $this->queryList("select * from customer__country order by name");
	    
	    return $l;
	}
	
	public function readAsMap() {
	    
	    $m = array();
	    $l = $this->queryList("select * from customer__country order by name");
	    
	    foreach($l as $country) {
	        $m[$country->getCountryId()] = $country->getName();
	    }
	    
	    return $m;
	}

}

