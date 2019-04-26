<?php




use core\db\DatabaseHandler;
use core\exception\DatabaseException;

function query($dbhandler, $sql, $params=array()) {
    
    if (is_array($params) == false)
        throw new InvalidArgumentException('params not an array');
    
    $dbh = DatabaseHandler::getInstance()->getResource( $dbhandler );
    
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
    
    DatabaseHandler::getInstance()->setLastQuery($str);
        
    $r = $dbh->query($str);
    
    if ($r === false) {
        $ex = new DatabaseException('SQL error: ' . $dbh->error . ' ('.$dbh->errno.')');
        $ex->setQuery($str);
        throw $ex;
    }
    
    return $r;
}

function queryList($dbHandler, $sql, $params=array()) {
    $res = query($dbHandler, $sql, $params);
    
    $rows = array();
    while($row = $res->fetch_assoc()) {
        $rows[] = $row;
    }
    
    return $rows;
}

function queryListAsArray($dbHandler, $sql, $params=array()) {
    if (is_array($params) == false)
        throw new InvalidArgumentException('params not an array');
        
    $res = query($dbHandler, $sql, $params);
    
    $rows = array();
    while($row = $res->fetch_array()) {
        $rows[] = $row;
    }
    
    return $rows;
}


function dbCamelCase($tableName) {
    $str = '';
    
    if (strpos($tableName, '__') !== false) {
        $tableName = substr($tableName, strpos($tableName, '__')+2);
    }
    
    $blnPrevWasUnderscore = true;
    
    for($x=0; $x < strlen($tableName); $x++) {
        if ($tableName{$x} != '_') {
            if ($blnPrevWasUnderscore) {
                $str .= strtoupper($tableName{$x});
            } else {
                $str .= $tableName{$x};
            }
        }
        
        $blnPrevWasUnderscore = ($tableName{$x} == '_') ? true : false;
    }
    
    return $str;
}


function filterOrderBy($str) {
    return preg_replace('/[^a-zA-Z_, ]/', '', $str);
}



