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
	
	
	public function listFolders() {
	    $sql = "select cif.folderName, count(e.email_id) value
                from webmail__connector_imapfolder cif
                left join webmail__email e on (cif.connector_imapfolder_id = e.connector_imapfolder_id)
                group by cif.connector_imapfolder_id
                order by cif.folderName='INBOX' desc, cif.folderName";
	    
	    $l = $this->queryList( $sql );
	    
	    $folders = array();
	    foreach($l as $i) {
	        $folders[] = ['name' => $i->getField('folderName'), 'value' => $i->getField('value')];
	    }
	    
	    return $folders;
	}
	
	
}

