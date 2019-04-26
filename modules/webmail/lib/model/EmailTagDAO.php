<?php


namespace webmail\model;


class EmailTagDAO extends \core\db\DAOObject {

	public function __construct() {
		$this->setResource( 'default' );
		$this->setObjectName( '\\webmail\\model\\EmailTag' );
	}
	

}

