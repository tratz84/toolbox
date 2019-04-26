<?php


namespace webmail\model;


class FilterDAO extends \core\db\DAOObject {

	public function __construct() {
		$this->setResource( 'default' );
		$this->setObjectName( '\\webmail\\model\\Filter' );
	}
	
	
	public function readAll() {
	    return $this->queryList('select * from webmail__filter order by sort');
	}
	
	public function read($id) {
	    return $this->queryOne('select * from webmail__filter where filter_id = ?', array($id));
	}

	public function delete($id) {
	    return $this->query('delete from webmail__filter where filter_id = ?', array($id));
	}
	
	public function readByConnector($connectorId) {
	    return $this->queryList('select * from webmail__filter where connector_id = ? order by sort', array($connectorId));
	}
	
	public function nextSort() {
	    $s = $this->queryValue('select max(sort) from webmail__filter');
	    
	    return intval($s) + 1;
	}

}

