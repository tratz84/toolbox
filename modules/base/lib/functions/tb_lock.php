<?php




use core\db\DatabaseHandler;



function db_lock( $lockName, $timeout = 3600 ) {
    
    $n = ctx()->getContextName() . '.' . $lockName;
    
    if (strlen($n) > 64) {
        // generate notice.. should be fixed
        trigger_error( 'db_lock, lockName exceeds 64 chars: ' . $lockName, E_USER_NOTICE );
        
        $n = substr($n, 0, 64);
    }
    
    return DatabaseHandler::getInstance()->getConnection('default')->getLock( $n, $timeout );
}

function db_release_lock( $lockName ) {
    
    $n = ctx()->getContextName() . '.' . $lockName;
    
    if (strlen($n) > 64)
        $n = substr($n, 0, 64);
    
    return DatabaseHandler::getInstance()->getConnection('default')->releaseLock( $n );
}



