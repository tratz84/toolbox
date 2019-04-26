<?php


namespace project\model;


class ProjectHourStatusDAO extends \core\db\DAOObject {

	public function __construct() {
		$this->setResource( 'default' );
		$this->setObjectName( '\\project\\model\\ProjectHourStatus' );
	}
	
	
	
	
	public function read($id) {
	    return $this->queryOne('select * from project__project_hour_status where project_hour_status_id = ?', array($id));
	}
	
	public function delete($id) {
	    $this->query('delete from project__project_hour_status where project_hour_status_id = ?', array($id));
	}
	
	public function readAll() {
	    return $this->queryList('select * from project__project_hour_status order by sort');
	}
	
	
}

