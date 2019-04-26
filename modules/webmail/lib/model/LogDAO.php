<?php


namespace webmail\model;


class LogDAO extends \core\db\DAOObject {

	public function __construct() {
		$this->setResource( 'default' );
		$this->setObjectName( '\\webmail\\model\\Log' );
	}
	

}

