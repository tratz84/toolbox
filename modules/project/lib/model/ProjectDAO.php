<?php


namespace project\model;


class ProjectDAO extends \core\db\DAOObject {

	public function __construct() {
		$this->setResource( 'default' );
		$this->setObjectName( '\\project\\model\\Project' );
	}
	
	
	public function read($id) {
	    return $this->queryOne('select * from project__project where project_id = ?', array($id));
	}
	
	public function delete($id) {
	    return $this->query('delete from project__project where project_id = ?', array($id));
	}
	
	public function readByCompany($companyId) {
	    return $this->queryList('select * from project__project where company_id = ?', array($companyId));
	}
	
	public function readByPerson($personId) {
	    return $this->queryList('select * from project__project where person_id = ?', array($personId));
	}
	
	
	public function search($opts) {
	    $sql = "select p.*, c.company_id, c.company_name, person.person_id, person.firstname, person.insert_lastname, person.lastname, sum(ifnull(duration*60, TIMESTAMPDIFF(minute, start_time, end_time))) total_minutes
                from project__project p
                left join project__project_hour ph using (project_id)
                left join customer__company c using (company_id)
                left join customer__person person using (person_id) ";
	    
	    $where = array();
	    $params = array();
	    
	    
	    if (isset($opts['name']) && $opts['name']) {
			$where[] = "company_name like ? OR concat(person.firstname, ' ', person.insert_lastname, ' ', person.lastname) like ?";
			$params[] = '%' . $opts['name'] . '%';
			$params[] = '%' . $opts['name'] . '%';
		}

		if (isset($opts['project_name']) && $opts['project_name']) {
			$where[] = 'project_name like ?';
			$params[] = '%' . $opts['project_name'] . '%';
		}
	    
	    if (count($where)) {
	        $sql .= ' where (' . implode(') AND (', $where) . ")";
	    }
	    $sql .= ' group by p.project_id';
	    
	    $sql .= ' order by max(ph.project_hour_id) desc ';
	    
	    return $this->queryCursor($sql, $params);
	}
	

}

