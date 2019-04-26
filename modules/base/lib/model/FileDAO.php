<?php


namespace base\model;


class FileDAO extends \core\db\DAOObject {

	public function __construct() {
		$this->setResource( 'default' );
		$this->setObjectName( '\\base\\model\\File' );
	}
	

}

