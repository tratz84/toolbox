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

	public function readByCode($webmenuCode) {
	    $params = array();
        $sql = 'select * from fastsite__webmenu where code = ?';
        $params[] = $webmenuCode;
	    
	    return $this->queryOne($sql, $params);
	}
	
	public function readByParent($webmenuParentId) {
	    $webmenuParentId = (int)$webmenuParentId;
	    
	    $params = array();
	    if ($webmenuParentId > 0) {
    	    $sql = 'select * from fastsite__webmenu where parent_webmenu_id = ? order by sort';
    	    $params[] = $webmenuParentId;
	    } else {
	        $sql = 'select * from fastsite__webmenu where (parent_webmenu_id = "" OR parent_webmenu_id = 0 OR parent_webmenu_id IS NULL) order by sort';
	    }
	    
	    return $this->queryList($sql, $params);
	}
	
	
	public function delete($id) {
	    return $this->query('delete from fastsite__webmenu where webmenu_id = ?', array($id));
	}
	
	

}

