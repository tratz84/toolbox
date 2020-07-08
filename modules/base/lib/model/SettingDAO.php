<?php


namespace base\model;


class SettingDAO extends \core\db\DAOObject {

	public function __construct() {
		$this->setResource( 'default' );
		$this->setObjectName( '\\base\\model\\Setting' );
	}
	

	public function readKeyValue() {
	    $sql = "select setting_code, text_value from base__setting";
	    return $this->queryList($sql);
	}
	
	public function readByKey($key) {
	    $sql = "select * from base__setting where setting_code = ?";
	    
	    $l = $this->queryList($sql, array($key));
	    if (count($l)) {
	        return $l[0];
	    } else {
	        return null;
	    }
	}
	
	
	public function readByType($type) {
	    $sql = "select * from base__setting where setting_type = ?";
	    
	    return $this->queryList($sql, array($type));
	}
}

