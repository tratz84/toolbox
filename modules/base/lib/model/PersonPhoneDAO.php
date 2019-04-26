<?php


namespace base\model;


class PersonPhoneDAO extends \core\db\DAOObject {

	public function __construct() {
		$this->setResource( 'default' );
		$this->setObjectName( '\\base\\model\\PersonPhone' );
	}
	
	
	public function readByPerson($personId) {
	    return $this->queryList("select * from customer__person_phone where person_id = ?", array($personId));
	}
	
	public function delete($id) {
	    $this->query("delete from customer__person_phone where person_phone_id = ?", array($id));
	}

}

