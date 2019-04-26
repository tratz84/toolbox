<?php


namespace base\model;


class PersonAddressDAO extends \core\db\DAOObject {

	public function __construct() {
		$this->setResource( 'default' );
		$this->setObjectName( '\\base\\model\\PersonAddress' );
	}

	public function readByPerson($personId) {
	    return $this->queryList("select * from customer__person_address where person_id = ?", array($personId));
	}
	
	public function delete($id) {
	    $this->query("delete from customer__person_address where person_address_id = ?", array($id));
	}

}

