<?php


namespace calendar\model;


class CalendarItemStatusDAO extends \core\db\DAOObject {

	public function __construct() {
		$this->setResource( 'default' );
		$this->setObjectName( '\\calendar\\model\\CalendarItemStatus' );
	}
	

}

