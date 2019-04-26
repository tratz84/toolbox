<?php


namespace calendar\model;


class TodoItemDAO extends \core\db\DAOObject {

	public function __construct() {
		$this->setResource( 'default' );
		$this->setObjectName( '\\calendar\\model\\TodoItem' );
	}
	

}

