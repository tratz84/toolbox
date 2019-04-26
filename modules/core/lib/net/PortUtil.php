<?php

namespace core\net;

class PortUtil {
    
    
    
    public static function listOpenPorts() {
        $ports = array();
        
        $netstatOutput = null;
        $cmds = ['/usr/bin/netstat', '/bin/netstat', getenv('windir').'/system32/netstat.exe'];
        foreach($cmds as $cmd) {
            if (file_exists($cmd)) {
                $netstatOutput = `$cmd -an`;
                break;
            }
        }
        
        if ($netstatOutput == null)
            return null;
        
        $lines = explode("\n", $netstatOutput);
        foreach($lines as $l) {
            $l = trim($l);
            
            $tokens = preg_split("/\\s+/", $l);
            
            $src = null;
            $dst = null;
            
            if (count($tokens) == 0) continue;
            if (strtoupper($tokens[0]) != 'TCP' && strtoupper($tokens[0]) != 'TCP6') continue;
            
            $matches = array();
            preg_match_all('/(:\\d+|\\*)/', $l, $matches);
            if (count($matches)) {
                $p = (int)substr($matches[0][0], 1);
                
                if (in_array($p, $ports) == false) {
                    $ports[] = $p;
                }
            }
        }
        
        return $ports;
    }
}


