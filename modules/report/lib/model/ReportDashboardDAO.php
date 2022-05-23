<?php


namespace report\model;


class ReportDashboardDAO extends \core\db\DAOObject {

	public function __construct() {
		$this->setResource( 'default' );
		$this->setObjectName( '\\report\\model\\ReportDashboard' );
	}
	

}

