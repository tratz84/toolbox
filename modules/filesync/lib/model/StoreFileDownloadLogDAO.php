<?php


namespace filesync\model;


class StoreFileDownloadLogDAO extends \core\db\DAOObject {

	public function __construct() {
		$this->setResource( 'default' );
		$this->setObjectName( '\\filesync\\model\\StoreFileDownloadLog' );
	}
	

}

