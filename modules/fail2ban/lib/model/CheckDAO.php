<?php


namespace fail2ban\model;


class CheckDAO extends \core\db\DAOObject {

	public function __construct() {
		$this->setResource( 'default' );
		$this->setObjectName( '\\fail2ban\\model\\Check' );
	}
	

}

