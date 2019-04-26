<?php


namespace calendar\model;


class CalendarItemCategoryDAO extends \core\db\DAOObject {

	public function __construct() {
		$this->setResource( 'default' );
		$this->setObjectName( '\\calendar\\model\\CalendarItemCategory' );
	}
	

}

