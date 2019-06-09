<?php


namespace project\model;


use core\db\query\QueryBuilderWhere;

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
	    $qb = $this->createQueryBuilder();
	    
	    $qb->selectFields('project__project_hour.*', 'customer__company.company_name');
	    $qb->selectFields('customer__person.firstname', 'customer__person.insert_lastname', 'customer__person.lastname');
	    $qb->selectFields('project__project.project_name', 'project__project_hour_type.description type_description', 'project__project_hour_status.description status_description');
	    $qb->selectFields('base__user.username');
	    $qb->selectFunction("ifnull(duration*60, TIMESTAMPDIFF(minute, start_time, end_time)) total_minutes");
	    
	    $qb->setTable('project__project_hour');
	    $qb->leftJoin('project__project',             'project_id');
	    $qb->leftJoin('project__project_hour_type',   'project_hour_type_id');
	    $qb->leftJoin('project__project_hour_status', 'project_hour_status_id');
	    $qb->leftJoin('customer__company',            'company_id', 'project__project');
	    $qb->leftJoin('customer__person',             'person_id', 'project__project');
	    $qb->leftJoin('base__user',                   'user_id');
	    
	    
	    if (isset($opts['project_id']) && $opts['project_id']) {
	        $qb->addWhere(QueryBuilderWhere::whereRefByVal('project__project_hour.project_id', '=', $opts['project_id']));
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
	        $qb->addWhere(QueryBuilderWhere::whereRefByVal('project__project.company_id', '=', $opts['company_id']));
	    }
	    
	    if (isset($opts['person_id']) && $opts['person_id']) {
	        $qb->addWhere(QueryBuilderWhere::whereRefByVal('project__project.person_id', '=', $opts['person_id']));
	    }
	    
	    if (isset($opts['project_hour_status_id']) && $opts['project_hour_status_id']) {
	        $qb->addWhere(QueryBuilderWhere::whereRefByVal('project__project_hour_status.project_hour_status_id', '=', $opts['project_hour_status_id']));
	    }
	    
	    if (isset($opts['start']) && $opts['start']) {
	        $qb->addWhere(QueryBuilderWhere::whereRefByVal('project__project_hour.start_time', '>=', format_date($opts['start'], 'Y-m-d 00:00:00')));
	    }
	    if (isset($opts['end']) && $opts['end']) {
	        $qb->addWhere(QueryBuilderWhere::whereRefByVal('project__project_hour.end_time', '<=', format_date($opts['end'], 'Y-m-d 23:59:59')));
	    }
	    
	    
	    $qb->setOrderBy('project__project_hour.start_time desc');
		
// 	    print $qb->createSelect();exit;
	    
	    return $qb->queryCursor(ProjectHour::class);
	}
	
	
	public function deleteByProject($projectId) {
	    $this->query('delete from project__project_hour where project_id = ?', array($projectId));
	}

}

