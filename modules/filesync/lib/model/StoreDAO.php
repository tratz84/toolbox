<?php


namespace filesync\model;


class StoreDAO extends \core\db\DAOObject {

	public function __construct() {
		$this->setResource( 'default' );
		$this->setObjectName( '\\filesync\\model\\Store' );
	}
	
	
	public function read($id) {
	    return $this->queryOne('select * from filesync__store where store_id = ?', array($id));
	}
	
	public function readByName($name) {
	    return $this->queryOne('select * from filesync__store where store_name = ?', array($name));
	}
	
	public function delete($id) {
	    $this->query('delete from filesync__store where store_id = ?', array($id));
	}
	
	public function readAll() {
	    return $this->queryList('select * from filesync__store order by store_name');
	}

	
	public function updateLastFileChange($storeId, $time) {
	    $this->query('update filesync__store set last_file_change = ? where store_id = ?', array($time, $storeId));
	}
	
	public function readArchives() {
	    return $this->queryList("select * from filesync__store where store_type='archive' order by store_name");
	}
	
}

