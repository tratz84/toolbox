<?php


namespace fastsite\model;


class WebmenuDAO extends \core\db\DAOObject {

	public function __construct() {
		$this->setResource( 'default' );
		$this->setObjectName( '\\fastsite\\model\\Webmenu' );
	}
	
	
	public function readAll() {
	    return $this->queryList('select * from fastsite__webmenu');
	}
	
	
	public function read($id) {
        return $this->queryOne('select * from fastsite__webmenu where webmenu_id = ?', array($id));
	}
	
	
	public function delete($id) {
	    return $this->query('delete from fastsite__webmenu where webmenu_id = ?', array($id));
	}
	
	

}

