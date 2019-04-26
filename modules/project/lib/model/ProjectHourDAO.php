<?php


namespace project\model;


use core\db\DatabaseHandler;

class ProjectHourDAO extends \core\db\DAOObject {

	public function __construct() {
		$this->setResource( 'default' );
		$this->setObjectName( '\\project\\model\\ProjectHour' );
	}
	
	public function statusToNull($id) {
	    $this->query("update project__project_hour set project_hour_status_id = null where project_hour_status_id = ?", array($id));
	}
	
	public function typeToNull($id) {
	    $this->query("update project__project_hour set project_hour_type_id = null where project_hour_type_id = ?", array($id));
	}
	
	public function read($id) {
	    return $this->queryOne('select * from project__project_hour where project_hour_id = ?', array($id));
	}
	
	public function delete($id) {
	    return $this->query('delete from project__project_hour where project_hour_id = ?', array($id));
	}
	
	public function setStatusId($projectHourId, $projectHourStatusId) {
	    $this->query('update project__project_hour set project_hour_status_id = ? where project_hour_id = ?', array($projectHourStatusId, $projectHourId));
	}
	
	
	public function search($opts) {
	    $sql = "select ph.*, c.company_name, person.firstname, person.insert_lastname, person.lastname, p.project_name, pht.description type_description, phs.description status_description, u.username,  ifnull(duration*60, TIMESTAMPDIFF(minute, start_time, end_time)) total_minutes
                from project__project_hour ph
                left join project__project p using (project_id)
                left join project__project_hour_type pht using (project_hour_type_id)
                left join project__project_hour_status phs using (project_hour_status_id)
                left join customer__company c using (company_id)
                left join customer__person person using (person_id)
                left join base__user u using (user_id) ";
	    
	    $where = array();
	    $params = array();
	    
	    if (isset($opts['project_id']) && $opts['project_id']) {
	        $where[] = ' ph.project_id = ? ';
	        $params[] = $opts['project_id'];
	    }
	    
	    if (isset($opts['customer_id'])) {
	        if (strpos($opts['customer_id'], 'company-') === 0) {
	            $opts['company_id'] = str_replace('company-', '', $opts['customer_id']);
	        }
	        if (strpos($opts['customer_id'], 'person-') === 0) {
	            $opts['person_id'] = str_replace('person-', '', $opts['customer_id']);
	        }
	    }
	    
	    
	    if (isset($opts['company_id']) && $opts['company_id']) {
	        $where[] = ' p.company_id = ? ';
	        $params[] = $opts['company_id'];
	    }
	    
	    if (isset($opts['person_id']) && $opts['person_id']) {
	        $where[] = ' p.person_id = ? ';
	        $params[] = $opts['person_id'];
	    }
	    
	    if (isset($opts['project_hour_status_id']) && $opts['project_hour_status_id']) {
	        $where[] = 'phs.project_hour_status_id = ?';
	        $params[] = $opts['project_hour_status_id'];
	    }
	    
	    if (isset($opts['start']) && $opts['start']) {
	        $where[] = 'ph.start_time >= ?';
	        $params[] = format_date($opts['start'], 'Y-m-d 00:00:00');
	    }
	    if (isset($opts['end']) && $opts['end']) {
	        $where[] = 'ph.end_time <= ?';
	        $params[] = format_date($opts['end'], 'Y-m-d 23:59:59');
	    }
	    
	    
	    if (count($where)) {
	        $sql .= ' where ('.implode(') AND (', $where) . ') ';
	    }
		
		$sql .= " order by ph.start_time desc ";
	    
	    return $this->queryCursor($sql, $params);
	}
	
	
	public function deleteByProject($projectId) {
	    $this->query('delete from project__project_hour where project_id = ?', array($projectId));
	}

}

