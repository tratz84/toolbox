<?php


namespace customer\model;


class PersonEmailDAO extends \core\db\DAOObject {

	public function __construct() {
		$this->setResource( 'default' );
		$this->setObjectName( '\\customer\\model\\PersonEmail' );
	}
	
	
	public function readByPerson($personId) {
	    return $this->queryList("select * from customer__person_email where person_id = ?", array($personId));
	}
	
	public function delete($id) {
	    $this->query("delete from customer__person_email where person_email_id = ?", array($id));
	}

}

