<?php


namespace filesync\model;


class StoreFileMetaTagDAO extends \core\db\DAOObject {

	public function __construct() {
		$this->setResource( 'default' );
		$this->setObjectName( '\\filesync\\model\\StoreFileMetaTag' );
	}
	

}

