<?php




use core\db\DatabaseHandler;
use core\exception\DatabaseException;


function queryValue($resourceName, $sql, $params=array()) {
    $conn = DatabaseHandler::getInstance()->getConnection( $resourceName );
    
    return $conn->queryValue( $sql, $params );
}

function query($resourceName, $sql, $params=array()) {
    $conn = DatabaseHandler::getInstance()->getConnection( $resourceName );
    
    return $conn->query( $sql, $params );
}

function queryOne($resourceName, $sql, $params=array()) {
    $conn = DatabaseHandler::getInstance()->getConnection( $resourceName );
    
    return $conn->queryOne( $sql, $params );
}

function queryList($resourceName, $sql, $params=array()) {
    $conn = DatabaseHandler::getInstance()->getConnection( $resourceName );
    
    return $conn->queryList( $sql, $params );
}

function queryMap($resourceName, $sql, $params=array()) {
    $conn = DatabaseHandler::getInstance()->getConnection( $resourceName );
    
    $list = $conn->queryList( $sql, $params );
    
    $map = array();
    if (count($list) > 0) {
        $keys = array_keys($list[0]);
        $fkey = $keys[0];
        
        foreach( $list as $l ) {
            $map[$l[$fkey]] = $l;
        }
    }
    
    return $map;
}


function queryListAsArray($resourceName, $sql, $params=array()) {
    $conn = DatabaseHandler::getInstance()->getConnection( $resourceName );
    
    return $conn->queryListAsArray( $sql, $params );
}

function queryCursor($resourceName, $sql, $params=array(), $objectName = null) {
    $conn = DatabaseHandler::getInstance()->getConnection( $resourceName );
    
    return $conn->queryCursor( $objectName, $sql, $params );
}


function dbCamelCase($tableName) {
    $str = '';
    
    if (strpos($tableName, '__') !== false) {
        $tableName = substr($tableName, strpos($tableName, '__')+2);
    }
    
    $blnPrevWasUnderscore = true;
    
    for($x=0; $x < strlen($tableName); $x++) {
        if ($tableName[$x] != '_') {
            if ($blnPrevWasUnderscore) {
                $str .= strtoupper($tableName[$x]);
            } else {
                $str .= $tableName[$x];
            }
        }
        
        $blnPrevWasUnderscore = ($tableName[$x] == '_') ? true : false;
    }
    
    return $str;
}

function queryMarks( $count ) {
    if (is_array($count)) $count = count($count);
    
    $arr = array();
    
    for($x=0; $x < $count; $x++) {
        $arr[] = '?';
    }
    
    return implode(', ', $arr);
}


function filterOrderBy($str) {
    return preg_replace('/[^a-zA-Z_, ]/', '', $str);
}



