<?php


namespace calendar\model;


class TodoDAO extends \core\db\DAOObject {

	public function __construct() {
		$this->setResource( 'default' );
		$this->setObjectName( '\\calendar\\model\\Todo' );
	}
	

}

