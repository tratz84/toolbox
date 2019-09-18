<?php


namespace fastsite\model;


class WebformFieldDAO extends \core\db\DAOObject {

	public function __construct() {
		$this->setResource( 'default' );
		$this->setObjectName( '\\fastsite\\model\\WebformField' );
	}
	

}

