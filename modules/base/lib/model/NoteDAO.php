<?php


namespace base\model;


class NoteDAO extends \core\db\DAOObject {

	public function __construct() {
		$this->setResource( 'default' );
		$this->setObjectName( '\\base\\model\\Note' );
	}
	
	
	public function read($id) {
	    return $this->queryOne('select * from base__note where note_id = ?', array($id));
	}
	
	
	public function readByRef($refObj, $refId) {
	    $sql = "select * from base__note where ref_object = ? and ref_id = ? order by sort desc, note_id desc ";
	    
	    return $this->queryList($sql, array($refObj, $refId));
	}
	
	public function readByCompany($companyId) {
	    $sql = "select * from base__note where company_id = ? order by sort desc, note_id desc ";
	    
	    return $this->queryList($sql, array($companyId));
	}
	
	public function readByPerson($personId) {
	    $sql = "select * from base__note where person_id = ? order by sort desc, note_id desc ";
	    
	    return $this->queryList($sql, array($personId));
	}
	
	
	public function delete($id) {
	    return $this->query('delete from base__note where note_id = ?', array($id));
	}

	public function maxSortByRef($ref_object, $ref_id) {
	    $sql = "select max(sort) from base__note where ref_object = ? and ref_id = ?";
	    
	    return (int)$this->queryValue($sql, array($ref_object, $ref_id));
	}
	
	public function maxSortByCustomer($company_id, $person_id) {
	    if ($company_id) {
	        return (int)$this->queryValue("select max(sort) from base__note where company_id = ?", array($company_id));
	    }
	    if ($person_id) {
	        return (int)$this->queryValue("select max(sort) from base__note where person_id = ?", array($person_id));
	    }
	    
	    return 0;
	}
}

