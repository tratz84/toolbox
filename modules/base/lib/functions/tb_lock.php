<?php




use base\model\Lock;
use base\model\LockDAO;
use core\db\DatabaseHandler;

function tb_lock_time( $lockname, $lock_seconds=null, $timeout=0 ) {
    $dt = date('Y-m-d H:i:s', time() + $lock_seconds);
    
    return tb_lock( $lockname, $dt, $timeout );
}

function tb_lock( $lockname, $expires=null, $timeout=0 ) {
    $lDao = new LockDAO();
    $lDao->cleanupLocks();
    
    $l = new Lock();
    $l->setLockName( $lockname );
    $l->setExpires( $expires );
    $l->setCreated( date('Y-m-d H:i:s') );
    
    $start = time();
    
    do {
        try {
            $l->save();
            return true;
        } catch (\Exception $ex) { }
        
        if ( $timeout )
            usleep( 300000 );
    } while ( ($start+$timeout) > time() );
    
    return false;
}

function tb_release_lock( $lockname ) {
    $ldao = new LockDAO();
    
    return $ldao->deleteLock($lockname);
}



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



