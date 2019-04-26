<?php


namespace filesync\model;


class StoreFileRevDAO extends \core\db\DAOObject {

	public function __construct() {
		$this->setResource( 'default' );
		$this->setObjectName( '\\filesync\\model\\StoreFileRev' );
	}
	
	
	public function read($id) {
	    return $this->queryOne('select * from filesync__store_file_rev where store_file_rev_id = ?', array($id));
	}

	public function delete($id) {
	    $this->query('delete from filesync__store_file_rev where store_file_rev_id = ?', array($id));
	}
	
	public function readByFile($storeFileId) {
	    return $this->queryList("select * from filesync__store_file_rev where store_file_id = ? order by rev", array($storeFileId));
	}
	
	public function deleteByFile($storeFileId) {
	    $this->query('delete from filesync__store_file_rev where store_file_id = ?', array($storeFileId));
	}
	
	
	public function readLastRevision($storeFileId) {
	    return $this->queryOne('select * from filesync__store_file_rev where store_file_id = ? order by rev desc', array($storeFileId));
	}
	
	public function getStoreSize($storeId) {
	    $sql = "select sum(filesize)
                from filesync__store_file sf
                left join filesync__store_file_rev sfr using (store_file_id)
                where store_id = ? ";
	    
	    return $this->queryValue($sql, array($storeId));
	}
	
	public function getStoreSizeActiveFiles($storeId) {
	    $sql = "select sum(filesize)
                from filesync__store_file sf
                left join filesync__store_file_rev sfr using (store_file_id)
                where sf.store_id = ? and sf.rev = sfr.rev ";
	    
	    return $this->queryValue($sql, array($storeId));
	}

}

