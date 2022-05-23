<?php


namespace report\model;


class ReportDashboard extends base\ReportDashboardBase {

	public function __construct($id=null) {
		parent::__construct( $id );
		
		
		$this->setActive( true );
		
	}
}

