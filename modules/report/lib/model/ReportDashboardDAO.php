<?php


namespace report\model;


class ReportDashboardDAO extends \core\db\DAOObject {

	public function __construct() {
		$this->setResource( 'default' );
		$this->setObjectName( '\\report\\model\\ReportDashboard' );
	}
	
	
	public function read( $id ) {
	    $sql = "select * 
                from report__report_dashboard 
                where report_dashboard_id = ?";
	    
	    return $this->queryOne( $sql, array($id) );
	}
	
	
	public function readAll() {
	    $sql = "select *
                from report__report_dashboard
                order by active desc, name";
	    
	    return $this->queryList( $sql );
	}
	
	public function readActive() {
	    $sql = "select *
                from report__report_dashboard
                order by name";
	    
	    return $this->queryList( $sql );
	}
	
}

