<?php


namespace webmail\model;


class EmailEmailTagDAO extends \core\db\DAOObject {

	public function __construct() {
		$this->setResource( 'default' );
		$this->setObjectName( '\\webmail\\model\\EmailEmailTag' );
	}
	

}

