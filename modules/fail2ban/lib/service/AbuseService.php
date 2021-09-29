<?php


namespace fail2ban\service;


class AbuseService extends ServiceBase {
    
    
    
    
    
    public function markAbuse( $ip, $message ) {
        if ($message && strlen($message) > 64) {
            $message = substr($message, 0, 64);
        }
        
        $ac = new BanCheck();
        $ac->setIp( $ip );
        $ac->setMessage( $message );
        $ac->save();
        
    }
    
    
    public function checkAbuse( $ip ) {
        if (is_debug()) {
            return false;
        }
        
        $acDao = new BanCheckDAO();
        $count = $acDao->abuseCount($ip, date('Y-m-d H:i:s', strtotime('-5 minutes')));
        if ($count > 10) {
            return 'Maximum failed attempts reached';
        }
        
        
        if (strpos($ip, ':') === false) {
            $iplong = ip2long( $ip );
            
            // check by netmask 255.255.255.0
            $count = $acDao->abuseCountNetmaskIpv4( $iplong, '0xffffff00', date('Y-m-d H:i:s', strtotime('-60 minutes')) );
            
            if ($count > 60 ) {
                return 'Too many attempts from your network';
            }
        }
        
        return false;
    }
    
}


