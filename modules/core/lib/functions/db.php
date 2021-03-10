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

function queryList($resourceName, $sql, $params=array()) {
    $conn = DatabaseHandler::getInstance()->getConnection( $resourceName );
    
    return $conn->queryList( $sql, $params );
}

function queryListAsArray($resourceName, $sql, $params=array()) {
    $conn = DatabaseHandler::getInstance()->getConnection( $resourceName );
    
    return $conn->queryListAsArray( $sql, $params );
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


function filterOrderBy($str) {
    return preg_replace('/[^a-zA-Z_, ]/', '', $str);
}



