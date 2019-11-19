<?php

namespace core\db\connection;

use core\db\DatabaseHandler;
use core\db\query\MysqlCursor;
use core\db\query\MysqlQueryBuilder;
use core\exception\DatabaseException;
use InvalidArgumentException;


class MysqlConnection extends DBConnection {
    
    protected $mysqli = null;
    
    protected $host;
    protected $port = 3306;
    protected $username;
    protected $password;
    protected $databaseName;
    
    protected $lastQuery = null;
    protected $transactionCount = 0;
    
    public function __construct() {
        
    }
    
    public function getResource() { return $this->mysqli; }
    
    public function getHost() { return $this->host; }
    public function setHost($h) { $this->host = $h; }

    public function setPort($p) { $this->port = $p; }
    public function getPort() { return $this->port; }
    
    public function setUsername($u) { $this->username = $u; }
    public function getUsername() { return $this->username; }
    
    public function setPassword($p) { $this->password = $p; }
    public function getPassword() { return $this->password; }
    
    public function setDatabaseName($n) { $this->databaseName = $n; }
    public function getDatabaseName() { return $this->databaseName; }
    
    public function getLastQuery() { return $this->lastQuery; }
    
    public function connect() {
        $this->mysqli = new \mysqli($this->host, $this->username, $this->password, $this->databaseName);
        
        if ($this->mysqli->connect_errno) {
            $this->error = 'Unable to connect to database';
            return false;
        }
        
        $this->mysqli->query('SET NAMES utf8');
        $this->mysqli->query('SET SQL_MODE=\'ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION\'');
        
        return true;
    }
    public function disconnect() {
        $this->mysqli->close();
    }
    
    public function beginTransaction() {
        
        $this->transactionCount++;
        
        if ($this->transactionCount > 1)
            return true;
        
        $r = $this->mysqli->begin_transaction(MYSQLI_TRANS_START_READ_WRITE);
        
        if (!$r) {
            // begin_transaction failed, throw an exception?
        }
        
        return $r;
    }
    
    
    public function commitTransaction() {
        $this->transactionCount--;
        
        if ($this->transactionCount < 0) {
            throw new DatabaseException('No transaction found');
        }
        
        
        if ($this->transactionCount == 0) {
            return $this->mysqli->commit();
        } else {
            return true;
        }
    }
    
    
    public function rollbackTransaction() {
        $this->transactionCount--;
        
        if ($this->transactionCount < 0) {
            throw new DatabaseException('No transaction found');
        }
        
        if ($this->transactionCount == 0) {
            return $this->mysqli->rollback();
        } else {
            return true;
        }
    }
    
    
    public function getInsertId() {
        return $this->mysqli->insert_id;
    }
    
    
    public function query($sql, $params=array()) {
        if (is_array($params) == false)
            throw new InvalidArgumentException('params not an array');
            
        $dbh = $this->getResource();
        
        $markCount = 0;
        $str = '';
        for($x=0; $x < strlen($sql); $x++) {
            if ($sql{$x} == '?') {
                // check if param is available
                if (count($params) < $markCount+1)
                    throw new \core\exception\QueryException("Invalid ratio marks(?)/params");
                    
                    $str .= "'".$dbh->real_escape_string($params[$markCount])."'";
                    $markCount++;
            } else {
                $str .= $sql{$x};
            }
        }
        
        if (count($params) > $markCount)
            throw new \core\exception\QueryException("Invalid ratio marks(?)/params");
        
        $this->lastQuery = $str;
        DatabaseHandler::getInstance()->setLastQuery($str);
        
        $r = $dbh->query($str);
        
        if ($r === false) {
            $ex = new DatabaseException('SQL error: ' . $dbh->error . ' ('.$dbh->errno.')');
            $ex->setQuery($str);
            throw $ex;
        }
        
        return $r;
    }
    
    function escape($str) {
        $dbh = $this->getResource();
        
        return $dbh->real_escape_string($str);
    }

    function queryOne($sql, $params=array()) {
        $res = $this->query($sql, $params);
        
        while($row = $res->fetch_assoc()) {
            $res->free();
            return $row;
        }
        
        return null;
    }
    
    function queryList($sql, $params=array()) {
        $res = $this->query($sql, $params);
        
        $rows = array();
        while($row = $res->fetch_assoc()) {
            $rows[] = $row;
        }
        
        return $rows;
    }
    
    function queryListAsArray($sql, $params=array()) {
        if (is_array($params) == false)
            throw new InvalidArgumentException('params not an array');
            
        $res = $this->query($sql, $params);
        
        $rows = array();
        while($row = $res->fetch_array()) {
            $rows[] = $row;
        }
        
        return $rows;
    }
    
    function queryCursor($objectName, $sql, $params = array()) {
        $res = $this->query($sql, $params);
        
        $cursor = new MysqlCursor($objectName, $res);
        
        return $cursor;
    }
    
    
    public function createQueryBuilder() {
        return new MysqlQueryBuilder( $this );
    }
    
}
