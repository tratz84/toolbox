<?php


namespace fail2ban\model;


class BlacklistDAO extends \core\db\DAOObject {

	public function __construct() {
		$this->setResource( 'default' );
		$this->setObjectName( '\\fail2ban\\model\\Blacklist' );
	}
	

}

