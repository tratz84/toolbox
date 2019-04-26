<?php


namespace webmail\model;


class EmailStatusDAO extends \core\db\DAOObject {

	public function __construct() {
		$this->setResource( 'default' );
		$this->setObjectName( '\\webmail\\model\\EmailStatus' );
	}
	

}

