<?php


namespace filesync\model;


class StoreFileMetaDAO extends \core\db\DAOObject {

	public function __construct() {
		$this->setResource( 'default' );
		$this->setObjectName( '\\filesync\\model\\StoreFileMeta' );
	}
	
	public function deleteByFile($storeFileId) {
	    $this->query('delete from filesync__store_file_meta where store_file_id = ?', array($storeFileId));
	}
	
	
	public function readByFile($storeFileId) {
	    return $this->queryOne('select * from filesync__store_file_meta where store_file_id = ?', array($storeFileId));
	}

}

