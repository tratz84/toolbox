<?php


namespace invoice\model;


class OfferFileDAO extends \core\db\DAOObject {

	public function __construct() {
		$this->setResource( 'default' );
		$this->setObjectName( '\\invoice\\model\\OfferFile' );
	}
	

}

