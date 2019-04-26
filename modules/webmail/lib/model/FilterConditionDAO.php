<?php


namespace webmail\model;


class FilterConditionDAO extends \core\db\DAOObject {

	public function __construct() {
		$this->setResource( 'default' );
		$this->setObjectName( '\\webmail\\model\\FilterCondition' );
	}
	
	
	public function readByFilter($filterId) {
	    return $this->queryList('select * from webmail__filter_condition where filter_id = ?', array($filterId));
	}

	public function deleteByFilter($filterId) {
		$this->query('delete from webmail__filter_condition where filter_id = ?', array($filterId));
	}


}

