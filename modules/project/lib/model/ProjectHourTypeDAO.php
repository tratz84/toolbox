<?php


namespace project\model;


class ProjectHourTypeDAO extends \core\db\DAOObject {

	public function __construct() {
		$this->setResource( 'default' );
		$this->setObjectName( '\\project\\model\\ProjectHourType' );
	}
	
	
	public function read($id) {
	    return $this->queryOne('select * from project__project_hour_type where project_hour_type_id = ?', array($id));
	}
	
	public function delete($id) {
	    $this->query('delete from project__project_hour_type where project_hour_type_id = ?', array($id));
	}
	
	public function readAll() {
	    return $this->queryList('select * from project__project_hour_type order by sort');
	}

}

