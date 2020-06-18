<?php


namespace customer\model;


class PersonAddressDAO extends \core\db\DAOObject {

	public function __construct() {
		$this->setResource( 'default' );
		$this->setObjectName( '\\customer\\model\\PersonAddress' );
	}

	public function readByPerson($personId) {
	    return $this->queryList("select * from customer__person_address where person_id = ?", array($personId));
	}
	
	public function delete($id) {
	    $this->query("delete from customer__person_address where person_address_id = ?", array($id));
	}

}

