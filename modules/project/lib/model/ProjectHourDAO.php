<?php


namespace project\model;


use core\db\query\QueryBuilderWhere;
use core\db\query\QueryBuilderWhereContainer;

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
	    $qb->selectFields('customer__person.person_id');
	    $qb->selectFields('project__project.project_name', 'project__project_hour_type.description type_description', 'project__project_hour_status.description status_description');
	    $qb->selectFields('base__user.username');
	    $qb->selectFields('customer__company.company_id');
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
	    
	    if (isset($opts['declarable'])) {
	        $opts['declarable'] = (string)$opts['declarable'];
	        if ($opts['declarable'] === '1' || $opts['declarable'] === '0') {
	           $qb->addWhere(QueryBuilderWhere::whereRefByVal('project__project_hour.declarable', '=', $opts['declarable']));
	        }
	    }
	    
	    
	    if (isset($opts['date']) && valid_date($opts['date'])) {
	        $qb->addWhere(QueryBuilderWhere::whereRefByVal('date(project__project_hour.start_time)', '=', format_date($opts['date'], 'Y-m-d')));
	    }
	    
	    if (isset($opts['start']) && $opts['start']) {
	        $qb->addWhere(QueryBuilderWhere::whereRefByVal('project__project_hour.start_time', '>=', format_date($opts['start'], 'Y-m-d 00:00:00')));
	    }
	    if (isset($opts['end']) && $opts['end']) {
	        $qbwc = new QueryBuilderWhereContainer('OR');
	        
	        // has start time
	        $qbwc->addWhere(QueryBuilderWhere::whereRefByVal('project__project_hour.end_time', '<=', format_date($opts['end'], 'Y-m-d 23:59:59')));
	        
	        // on start time
	        $qbwcEndTimeNull = new QueryBuilderWhereContainer();
	        $qbwcEndTimeNull->addWhere(QueryBuilderWhere::whereRefByRef('project__project_hour.end_time', 'IS', 'NULL'));
	        $qbwcEndTimeNull->addWhere(QueryBuilderWhere::whereRefByVal('project__project_hour.start_time', '<=', format_date($opts['end'], 'Y-m-d 23:59:59')));
	        $qbwc->addWhere($qbwcEndTimeNull);
	        
	        // gogogo
	        $qb->addWhere( $qbwc );
	    }
	    
	    
	    $qb->setOrderBy('project__project_hour.start_time desc');
		
// 	    print $qb->createSelect();exit;
	    
	    return $qb->queryCursor(ProjectHour::class);
	}
	
	
	public function deleteByProject($projectId) {
	    $this->query('delete from project__project_hour where project_id = ?', array($projectId));
	}
	
	
	public function readFirstStartTime() {
	    $sql = "select date(start_time) 
                from project__project_hour 
                order by start_time asc 
                limit 1";
	    
	    return $this->queryValue( $sql );
	}
	
	
	public function userSummaryForMonth($userId, $year, $month) {
	    $qb = $this->createQueryBuilder();
	    $qb->setTable('project__project_hour');
	    $qb->selectFunction("ifnull(duration*60, TIMESTAMPDIFF(minute, start_time, end_time)) total_minutes");
	    $qb->selectFunction("day(start_time) day");
	    
	    $qb->addWhere(QueryBuilderWhere::whereRefByVal('year(start_time)', '=', $year));
	    $qb->addWhere(QueryBuilderWhere::whereRefByVal('month(start_time)', '=', $month));
	    
	    $qb->getGroupBy('day(start_time)');
	    
	    $select = $qb->createSelect();
	    $params = $qb->getParams();
	    
	    $res = $this->query($select, $params);
	    $map = array();
	    while($row = $res->fetch_object()) {
	        if (isset($map[$row->day]) == false) $map[$row->day] = 0;
	        $map[$row->day] += $row->total_minutes;
	    }
	    
	    return $map;
	}
	
	

}

