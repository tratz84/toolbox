<?php


namespace webmail\model;


class FilterActionDAO extends \core\db\DAOObject {

	public function __construct() {
		$this->setResource( 'default' );
		$this->setObjectName( '\\webmail\\model\\FilterAction' );
	}
	
	
	public function readByFilter($filterId) {
	    return $this->queryList('select * from webmail__filter_action where filter_id = ?', array($filterId));
	}

	public function deleteByFilter($filterId) {
		$this->query('delete from webmail__filter_action where filter_id = ?', array($filterId));
	}

}

