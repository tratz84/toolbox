<?php


namespace fastsite\model;


class WebmenuDAO extends \core\db\DAOObject {

	public function __construct() {
		$this->setResource( 'default' );
		$this->setObjectName( '\\fastsite\\model\\Webmenu' );
	}
	

}

