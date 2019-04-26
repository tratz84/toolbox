<?php


namespace base\model;


class ObjectMetaDAO extends \core\db\DAOObject {

	public function __construct() {
		$this->setResource( 'default' );
		$this->setObjectName( '\\base\\model\\ObjectMeta' );
	}
	
	
	public function readByKey($objectName, $objectId, $objectKey) {
	    
	    $sql = "select * from base__object_meta where object_name = ? and object_id = ? and object_key = ?";
	    
	    $l = $this->queryList($sql, array($objectName, $objectId, $objectKey));
	    
	    if (count($l)) {
	        return $l[0];
	    } else {
	        return null;
	    }
	}

	
	public function readByValue($objectName, $objectKey, $objectValue) {
	    
	    $sql = "select * from base__object_meta where object_name = ? and object_key = ? and object_value = ?";
	    
	    $l = $this->queryList($sql, array($objectName, $objectKey, $objectValue));
	    
	    if (count($l)) {
	        return $l[0];
	    } else {
	        return null;
	    }
	}
	
	public function readByObject($objectName, $objectId) {
	    $sql = "select * from base__object_meta where object_name = ? and object_id = ?";
	    
	    return $this->queryList($sql, array($objectName, $objectId));
	}
	
	public function deleteByObject($objectName, $objectId) {
	    $sql = "delete from base__object_meta where object_name = ? and object_id = ?";
	    
	    $this->query($sql, array($objectName, $objectId));
	}
	
	
	public function search($opts=array()) {
	    $sql = 'select * 
	            from base__object_meta ';
	    
	    $where = array();
	    $params = array();
	    
	    if (isset($opts['object_name']) && $opts['object_name']) {
	        $where[] = " object_name = ? ";
	        $params[] = $opts['object_name'];
	    }
	    
	    if (isset($opts['object_key']) && $opts['object_key']) {
	        $where[] = " object_key = ? ";
	        $params[] = $opts['object_key'];
	    }
	    
	    if (isset($opts['object_key_like']) && $opts['object_key_like']) {
	        $where[] = " object_key LIKE ? ";
	        $params[] = $opts['object_key_like'];
	    }
	    
	    if (isset($opts['object_id']) && $opts['object_id']) {
	        $where[] = " object_id = ? ";
	        $params[] = $opts['object_id'];
	    }
	    
	    
	    if (count($where)) {
	        $sql .= " WHERE (" . implode(') AND (', $where) . ") ";
	    }
	    
	    return $this->queryCursor($sql, $params);
	}
	
}

