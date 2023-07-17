<?php


namespace webmail\model;


class WebmailAzureTokenDAO extends \core\db\DAOObject {

	public function __construct() {
		$this->setResource( 'default' );
		$this->setObjectName( '\\webmail\\model\\WebmailAzureToken' );
	}
	
	
	public function readAll() {
	    $sql = "select * from webmail__webmail_azure_token";
	    
	    return $this->queryList( $sql );
	}
	

}

