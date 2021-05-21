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

function print_info($str) {
    print '[' . ctx()->getContextName() . '][' . date('Y-m-d H:i:s') . '] ' . $str . "\n";
}

function print_cli_info($str) {
    if (is_cli()) {
        return print_info($str);
    }
}

function log_exception($ex, $opts=array()) {
    
    try {
        
        if (is_a($ex, core\exception\ContextNotFoundException::class)) {
            // don't save ContextNotFound-Exceptions
        } else {
            $ctx = \core\Context::getInstance();
            
            $el = new admin\model\ExceptionLog();
            $el->setContextName($ctx->getContextName());
            if ($ctx->getUser())
                $el->setUserId($ctx->getUser()->getUserId());
            $el->setRequestUri(substr($_SERVER['REQUEST_URI'], 0, 255));
            $el->setMessage($ex->getMessage());
            
            $stacktrace = '';
            if (is_a($ex, core\exception\DatabaseException::class)) {
                $stacktrace .= 'Query: '.$ex->getQuery() . "\n\n";
            }
            $stacktrace .= $ex->getFile() . ' ('.$ex->getLine().')' . "\n";
            $stacktrace .= $ex->getTraceAsString();
            $el->setStacktrace($stacktrace);
            $el->setParameters(var_export($_REQUEST, true));
            $el->save();
        }
    } catch (\Exception $ex) { }
    
    
    if (isset($opts['admin_notification']) && $opts['admin_notification']) {
        debug_admin_notification('Error: ' . ctx()->getContextName() . ': ' . $ex->getMessage());
    }
}



