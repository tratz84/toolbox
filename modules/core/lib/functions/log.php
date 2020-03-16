<?php




use core\exception\FileException;

/**
 * log_line() - logs message, prepends it with datetime
 * 
 * @param $file - path inside data-directory
 */
function log_line($file, $message) {
    $message = '['.date('Y-m-d H:i:s').']'."\t".$message."\n";
    
    return log_to_file($file, $message);
}

/**
 * log_to_file() - logs message to file
 * 
 * @param $file - path inside data-directory
 */
function log_to_file($file, $message) {
    // check if file exists
    $fullpath = get_data_file($file);
    if (!$fullpath) {
        if (!save_data($file, ''))
            throw new FileException('Unable to create log-file');
        
        $fullpath = get_data_file($file);
    }
    
    return log_to_filesystem($fullpath, $message);
}

/**
 * log_to_filesystem() - log message to path on filesystem
 * 
 * @param $fullpath - path on server
 */
function log_to_filesystem($fullpath, $message) {
    $fh = fopen($fullpath, 'a');
    
    if (!$fh) {
        return false;
    }
    
    // for non-blocking, flock($fp, LOCK_EX|LOCK_NB)
    
    if (flock($fh, LOCK_EX)) {
        // got lock, move to EOF
        fseek($fh, 0, SEEK_END);
        
        // write message
        fwrite($fh, $message);
        
        // guess these aren't necessary, because fclose should handle both
        //         fflush($fh);
        //         flock($fh, LOCK_UN);
        
        // close
        fclose($fh);
        
        return true;
    } else {
        return false;
    }
}



