<?php


namespace project\model;


use core\db\query\QueryBuilderWhere;
use core\db\query\QueryBuilderWhereContainer;

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
	    $qb = $this->createQueryBuilder();
	    
	    $qb->setTable('project__project');
	    $qb->selectFields('project__project.*', 'customer__company.company_id', 'customer__company.company_name', 'customer__person.person_id', 'customer__person.firstname', 'customer__person.insert_lastname', 'customer__person.lastname');
	    $qb->selectFunction('sum(ifnull(duration*60, TIMESTAMPDIFF(minute, start_time, end_time))) total_minutes');
	    $qb->leftJoin('project__project_hour', 'project_id');
	    $qb->leftJoin('customer__company', 'company_id');
	    $qb->leftJoin('customer__person', 'person_id');
	    
	    if (isset($opts['name']) && $opts['name']) {
	        
	        $qbwc = new QueryBuilderWhereContainer('OR');
	        $qbwc->addWhere(QueryBuilderWhere::whereRefByVal('company_name', 'LIKE', '%' . $opts['name'] . '%'));
	        $qbwc->addWhere(QueryBuilderWhere::whereRefByVal("concat(customer__person.firstname, ' ', customer__person.insert_lastname, ' ', customer__person.lastname)", 'LIKE', '%' . $opts['name'] . '%'));
	        
	        $qb->addWhere($qbwc);
	    }

		if (isset($opts['project_name']) && $opts['project_name']) {
		    $qb->addWhere(QueryBuilderWhere::whereRefByVal('project_name', 'LIKE', '%' . $opts['project_name'] . '%'));
		}
	    
		$qb->setGroupBy('project__project.project_id');
		
		$qb->setOrderBy('max(project__project_hour.project_hour_id) desc ');
		
		return $qb->queryCursor( Project::class );
	}
	

}

