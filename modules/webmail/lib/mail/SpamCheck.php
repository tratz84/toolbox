<?php

namespace webmail\mail;

class SpamCheck {
    
    protected static $cmdDaemon = '/usr/bin/spamc --exitcode';
    protected static $cmdStandalone = '/usr/bin/spamassassin -L --exit-code';
    
    protected static $cmdMarkSpam = '/usr/spamassassin/bin/sa-learn --spam -f -';
    protected static $cmdMarkHam = '/usr/spamassassin/bin/sa-learn --ham';
    

    protected static function cmdExists($cmd) {
	    if (strpos($cmd, ' ') !== false) {
		    $cmd = substr($cmd, 0, strpos($cmd, ' '));
	    }

	    return file_exists($cmd);
    }
    
    public static function isSpam($emlFile) {
        if (false) {
            $spamCheckCmd = self::$cmdDaemon;
        } else {
            $spamCheckCmd = self::$cmdStandalone;
        }
        
        // default to ham, when spamassassin is not installed
        if (self::cmdExists($spamCheckCmd) == false) {
            return false;
        }
        
        $r = self::execute($spamCheckCmd, $emlFile);
        
        if ($r !== 0) {
            return true;
        } else {
            return false;
        }
    }
    
    public static function markHam($emlFile) {
        if (self::cmdExists(self::$cmdMarkHam) == false)
            return 0;
        
        return self::execute(self::$cmdMarkHam, $emlFile);
    }
    
    public static function markSpam($emlFile) {
        if (self::cmdExists(self::$cmdMarkSpam) == false)
            return 0;
        
        return self::execute(self::$cmdMarkSpam, $emlFile);
    }
    
    
    protected static function execute($cmd, $file) {
        $pipes = array();
        
        $desc = array(
            array("pipe","r"),
            array("pipe","w"),
            array("pipe","w")
        );
        
        $process = proc_open($cmd, $desc, $pipes);
        
        fwrite($pipes[0], file_get_contents($file));
        fclose($pipes[0]);
       
        while (!feof($pipes[1])) {
            fgets($pipes[1], 1024);
        }
       
        fclose($pipes[1]);
       
        $return_value = proc_close($process);
       
        return $return_value;
    }
    
}

