<?php


/**
 * lock_system() - create system wide lock
 */
function lock_system($lockname, $throwException=true) {
    $lockname = preg_replace('/[^a-zA-Z0-9_-]/', '', slugify($lockname));
    
    $ctx = \core\Context::getInstance();
    $tmpdir = $ctx->getDataDir() . '/tmp';
    
    if (file_exists($tmpdir) == false) {
        if (mkdir($tmpdir, 0755) == false) {
            throw new \core\exception\FileException('Unable to create temp folder');
        }
    }
    
    // check if lock already exists
    if (realpath( $tmpdir . '/' . $lockname . '.lock' ) != false) {
        if ($throwException) {
            throw new \core\exception\LockException('System locked: '.$lockname);
        } else {
            return false;
        }
    }
    
    // create lock
    $fh = fopen($tmpdir . '/' . $lockname . '.lock' , 'x' );
    if ($fh == false) {
        if ($throwException) {
            throw new \core\exception\LockException('System locked: '.$lockname);
        } else {
            return false;
        }
    }
    fwrite($fh, "locked");
    fclose($fh);
    
    return true;
}

/**
 * release_system_lock() - release system wide lock
 */
function release_system_lock($lockname) {
    $lockname = preg_replace('/[^a-zA-Z0-9_-]/', '', slugify($lockname));

    $ctx = \core\Context::getInstance();
    $tmpdir = $ctx->getDataDir() . '/tmp';

    $lockfile = $tmpdir . '/' . $lockname . '.lock';
    
    if (file_exists($lockfile)) {
        return @unlink($lockfile);
    }
    
    return false;
}

