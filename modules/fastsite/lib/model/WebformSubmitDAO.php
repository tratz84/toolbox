<?php


namespace fastsite\model;


class WebformSubmitDAO extends \core\db\DAOObject {

	public function __construct() {
		$this->setResource( 'default' );
		$this->setObjectName( '\\fastsite\\model\\WebformSubmit' );
	}
	

}

