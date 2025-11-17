<?php


namespace invoice\model;


class OrderLineDAO extends \core\db\DAOObject {

	public function __construct() {
		$this->setResource( 'default' );
		$this->setObjectName( '\\invoice\\model\\OrderLine' );
	}
	

}

