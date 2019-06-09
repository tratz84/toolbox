<?php

namespace core\db;


use core\db\connection\MysqlConnection;
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
        return $this->__getConnection($handle)->beginTransaction();
	}
	
	public function commitTransaction($handle='default') {
	    return $this->__getConnection($handle)->commitTransaction();
	}
	
	public function rollbackTransaction($handle='default') {
	    return $this->__getConnection($handle)->rollbackTransaction();
	}
	
	
	public static function getResource($aHandlerName) {
		$dh = DatabaseHandler::getInstance();
		
		return $dh->__getResource($aHandlerName);
	}
	
	public static function getConnection($resourceName) {
	    return self::getInstance()->__getConnection($resourceName);
	}
	
	function __getConnection($aHandlerName) {
	    if (array_key_exists($aHandlerName,$this->mDbhResources) === false && array_key_exists($aHandlerName, $this->mSettings) == false)
	        throw new \core\exception\DatabaseException('Invalid database resource requested ('.$aHandlerName.')');
	        
        if (array_key_exists($aHandlerName, $this->mDbhResources) == false) {
            
            if ($this->mSettings[$aHandlerName]['type'] == 'mysql') {
                $c = new MysqlConnection();
                $c->setHost($this->mSettings[$aHandlerName]['host']);
                $c->setUsername($this->mSettings[$aHandlerName]['username']);
                $c->setPassword($this->mSettings[$aHandlerName]['password']);
                $c->setDatabaseName($this->mSettings[$aHandlerName]['dbname']);
                
                if ($c->connect() == false) {
                    throw new \core\exception\DatabaseException('Unable to connect to database ('.$aHandlerName.')');
                }
            }
            
            $this->mDbhResources[$aHandlerName] = $c;
        }
        
        return $this->mDbhResources[$aHandlerName];
	}
	
	// __getResource() - deprecated, TODO: remove..
	function __getResource($aHandlerName) {
		$c = $this->__getConnection($aHandlerName);
		
		return $c->getResource();
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
	    if ($checkHandles) foreach($this->transactionHandles as $resourceName => $cnt) {
	        if ($cnt > 0) {
	            throw new DatabaseException('Open transaction for "'.$resourceName.'"');
	        }
	    }
	    
	    // close connections
	    foreach($this->mDbhResources as $resourceName => $dbconnection) {
	        $dbconnection->disconnect();
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


