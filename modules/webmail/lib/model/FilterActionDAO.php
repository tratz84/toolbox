<?php


namespace webmail\model;


class FilterActionDAO extends \core\db\DAOObject {

	public function __construct() {
		$this->setResource( 'default' );
		$this->setObjectName( '\\webmail\\model\\FilterAction' );
	}
	
	
	public function readByFilter($filterId) {
	    $sql = 'select * 
                from webmail__filter_action 
                where filter_id = ?
                order by sort';
	    
	    return $this->queryList($sql, array($filterId));
	}

	public function deleteByFilter($filterId) {
		$this->query('delete from webmail__filter_action where filter_id = ?', array($filterId));
	}

}

