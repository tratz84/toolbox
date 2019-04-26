<?php


namespace admin\model;


class ExceptionLogDAO extends \core\db\DAOObject {

	public function __construct() {
		$this->setResource( 'admin' );
		$this->setObjectName( '\\admin\\model\\ExceptionLog' );
	}

	
	public function read($id) {
	    return $this->queryOne("select * from insights__exception_log where exception_log_id = ?", array($id));
	}
	
	
	
	public function search($opts=array()) {
	    
	    $sql = "select * from insights__exception_log ";
	    
	    $where = array();
	    $params = array();
	    
	    
	    if (isset($opts['contextNames'])) {
	        
	        $contexts = array();
	        foreach($opts['contextNames'] as $cn) {
	            $cn = preg_replace('/[^a-zA-Z0-9_]/', '', $cn);
	            
	            if ($cn)
	                $contexts[] = $cn;
	        }
	        
	        $where[] = "contextName IN ('" . implode("', '", $contexts) . "') ";
	    }
	    
	    
	    if (count($where)) {
	        $sql .= "where ( " . implode(") AND (", $where) . ") ";
	    }
	    
	    $sql .= 'order by exception_log_id desc';
	    
	    return $this->queryCursor($sql, $params);
	}
	

}

