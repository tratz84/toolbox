<?php


namespace fastsite\model;


class WebpageMetaDAO extends \core\db\DAOObject {

	public function __construct() {
		$this->setResource( 'default' );
		$this->setObjectName( '\\fastsite\\model\\WebpageMeta' );
	}
	

}

