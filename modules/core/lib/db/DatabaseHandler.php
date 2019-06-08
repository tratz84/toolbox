<?php

namespace core\db;


use core\exception\DatabaseException;

class DatabaseHandler {
	var $mDbhResources;
	
	var $mSettings;
	
	protected $transactionHandles = array();
	
	
	protected $lastQuery = null;
	
	
	function __construct() {
		$this->mDbhResources = array();
		
		$this->mSettings = array();
	}
	
	public function setLastQuery($q) { $this->lastQuery = $q; }
	public function getLastQuery() { return $this->lastQuery; }
	
	public function beginTransaction($handle='default') {
	    
	    if (isset($this->transactionHandles[$handle]) == false)
	        $this->transactionHandles[$handle] = 0;
	    
        $this->transactionHandles[$handle]++;
        
        if ($this->transactionHandles[$handle] > 1)
	        return true;
	    
	    $res = $this->getResource($handle);
	    $r = $res->begin_transaction(MYSQLI_TRANS_START_READ_WRITE);
	    
	    if (!$r) {
	        // begin_transaction failed, throw an exception?
	    }
	    
	    return $r;
	}
	
	public function commitTransaction($handle='default') {
	    $this->transactionHandles[$handle]--;
	    
	    if ($this->transactionHandles[$handle] < 0) {
	        throw new DatabaseException('No transaction for "'.$handle.'"');
	    }
	    
	    
	    if ($this->transactionHandles[$handle] == 0) {
    	    $res = $this->getResource($handle);
    	    return $res->commit();
	    } else {
	        return true;
	    }
	}
	
	public function rollbackTransaction($handle='default') {
	    $this->transactionHandles[$handle]--;

	    if ($this->transactionHandles[$handle] < 0) {
	        throw new DatabaseException('No transaction for "'.$handle.'"');
	    }
	    
	    if ($this->transactionHandles[$handle] == 0) {
    	    $res = $this->getResource($handle);
    	    
    	    return $res->rollback();
	    } else {
	        return true;
	    }
	}
	
// 	public function rollbackAll() {
// 	    while (count($this->transactionHandles) > 0) {
// 	        $this->rollbackTransaction($this->transactionHandles[0]);
// 	    }
// 	}
	
	
	public static function getResource($aHandlerName) {
		$dh = DatabaseHandler::getInstance();
		
		return $dh->__getResource($aHandlerName);
	}
	
	public static function createQueryBuilder($resourceName) {
		$dh = DatabaseHandler::getInstance();
		if ($dh->mSettings[$resourceName]['type'] == 'mysql') {
			return new MysqlQueryBuilder( $resourceName );
		}
		
		return null;
	}
	
	function __getResource($aHandlerName) {
		if (array_key_exists($aHandlerName,$this->mDbhResources) === false && array_key_exists($aHandlerName, $this->mSettings) == false)
		    throw new \core\exception\DatabaseException('Invalid database resource requested ('.$aHandlerName.')');
		
		if (array_key_exists($aHandlerName, $this->mDbhResources) == false) {
			$host     = $this->mSettings[$aHandlerName]['host'];
			$username = $this->mSettings[$aHandlerName]['username'];
			$password = $this->mSettings[$aHandlerName]['password'];
			$dbname   = $this->mSettings[$aHandlerName]['dbname'];
			
			$res = new \mysqli($host, $username, $password, $dbname);
		  
			if ($res->connect_errno) {
			    throw new \core\exception\DatabaseException('Unable to connect to database ('.$aHandlerName.')');
			}
			$res->query('SET NAMES utf8');
			$res->query('SET SQL_MODE=\'ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION\'');
			
			$this->mDbhResources[$aHandlerName] = $res;
		}
		
		return $this->mDbhResources[$aHandlerName];
	}
	
	function addServer($aInternalName, $aHost, $aUsername, $aPassword, $aDatabasename) {
		
		$this->mSettings[$aInternalName] = array();
		$this->mSettings[$aInternalName]['type']     = 'mysql';
		$this->mSettings[$aInternalName]['host']     = $aHost;
		$this->mSettings[$aInternalName]['username'] = $aUsername;
		$this->mSettings[$aInternalName]['password'] = $aPassword;
		$this->mSettings[$aInternalName]['dbname']   = $aDatabasename;
		
	}
	
	public function closeAll($checkHandles=true) {
	    // check any open transactions (shouldn't happen, would be a severe bug)
	    if ($checkHandles) foreach($this->transactionHandles as $handle => $cnt) {
	        if ($cnt > 0) {
	            throw new DatabaseException('Open transaction for "'.$handle.'"');
	        }
	    }
	    
	    // close connections
	    foreach($this->mDbhResources as $handle => $mysqli) {
	        $mysqli->close();
	    }
	    
	    $this->mDbhResources = array();
	}
	
	
	
	/**
	 * 
	 * @return DatabaseHandler
	 */
	public static function &getInstance() {
		static $instance;
		
		if (!$instance) {
			$instance = new DatabaseHandler();
		}
		
		return $instance;
	}
}


