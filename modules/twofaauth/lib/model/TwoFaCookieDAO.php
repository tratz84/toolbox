<?php


namespace twofaauth\model;


class TwoFaCookieDAO extends \core\db\DAOObject {

	public function __construct() {
		$this->setResource( 'default' );
		$this->setObjectName( '\\twofaauth\\model\\TwoFaCookie' );
	}
	
	
	public function readByValue( $val ) {
	    return $this->queryOne('select * from twofaauth__cookie where cookie_value = ?', array($val));
	}

}

