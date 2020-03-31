<?php


namespace webmail\model;


class ConnectorDAO extends \core\db\DAOObject {

	public function __construct() {
		$this->setResource( 'default' );
		$this->setObjectName( '\\webmail\\model\\Connector' );
	}
	
	public function readAll() {
	    return $this->queryList('select * from webmail__connector order by description');
	}
	
	public function readActive() {
	    return $this->queryList('select * from webmail__connector where active = true order by description');
	}
	
	public function connectorCount() {
	    return $this->queryValue('select count(*) from webmail__connector');
	}

	public function activeConnectorCount() {
	    return $this->queryValue('select count(*) from webmail__connector where active = true');
	}
	
	public function read($id) {
	    return $this->queryOne('select * from webmail__connector where connector_id = ?', array($id));
	}
	
	
	public function delete($id) {
	    return $this->query('delete from webmail__connector where connector_id = ?', array($id));
	}
	
	
	public function search($opts=array()) {
	    $sql = "select * from webmail__connector ";
	    
	    $where = array();
	    $params = array();
	    
	    
	    if (count($where)) {
	        $sql .= ' WHERE ('.implode(') AND (', $where) . ')';
	    }
	    
	    return $this->queryCursor($sql, $params);
	}

}

