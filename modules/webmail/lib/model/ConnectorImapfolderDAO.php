<?php


namespace webmail\model;


class ConnectorImapfolderDAO extends \core\db\DAOObject {

	public function __construct() {
		$this->setResource( 'default' );
		$this->setObjectName( '\\webmail\\model\\ConnectorImapfolder' );
	}
	
	
	public function read($id) {
	    return $this->queryOne('select * from webmail__connector_imapfolder where connector_imapfolder_id = ?', array($id));
	}
	
	
	public function readByConnector($connectorId) {
	    return $this->queryList('select * from webmail__connector_imapfolder where connector_id = ? order by foldername', array($connectorId));
	}
	
	
	public function deleteByConnector($connectorId) {
	    return $this->query('delete from webmail__connector_imapfolder where connector_id = ?', array($connectorId));
	}

	public function readAll() {
	    return $this->queryList('select * from webmail__connector_imapfolder order by foldername');
	}
	
}

