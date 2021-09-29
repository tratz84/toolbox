<?php


namespace fail2ban\service;


use core\forms\lists\ListResponse;
use core\service\ServiceBase;
use fail2ban\model\BanCheck;
use fail2ban\model\BanCheckDAO;
use fail2ban\model\IpSettingDAO;

class AbuseService extends ServiceBase {
    
    
    
    public function searchBanCheck($start, $limit, $opts=array()) {
        $bcDao = new BanCheckDAO();
        
        $cursor = $bcDao->search( $opts );
        
        $r = ListResponse::fillByCursor($start, $limit, $cursor, array('ban_check_id', 'ip', 'message', 'created'));
        
        return $r;
    }
    
    
    public function findMatch( $ip ) {
        $isDao = new IpSettingDAO();
        
        $isl = $isDao->readActive();
        
        foreach( $isl as $is ) {
            if ($is->match( $ip )) {
                return $is;
            }
        }
        
        return null;
    }
    
    
    
    public function markAbuse( $ip, $message ) {
        
        // white-listed? => skip
        $is = $this->findMatch( $ip );
        if ($is && $is->getType() == 'allow') {
            return;
        }
        
        
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
//             return false;
        }
        
        
        // check white/black list
        $is = $this->findMatch( $ip );
        if ($is) {
            if ($is->getType() == 'allow') {
                return false;
            }
            if ($is->getType() == 'block') {
                return 'IP blocked';
            }
        }
        
        
        $maxAttemptsIp = fail2ban_max_attempts_ip();
        if ($maxAttemptsIp > 0) {
            $acDao = new BanCheckDAO();
            $count = $acDao->abuseCount($ip, date('Y-m-d H:i:s', strtotime('-'.fail2ban_max_attempts_ip_timespan().' minutes')));
            if ($count > 10) {
                return 'Maximum failed attempts reached';
            }
        }
        
        
        $maxAttemptsNetwork = fail2ban_max_attempts_network();
        if (strpos($ip, ':') === false) {
            // ipv4
            if ($maxAttemptsNetwork > 0) {
                $ipParts = explode('.', $ip);
                
                $ipSearch = $ipParts[0] . '.' . $ipParts[1] . '.' . $ipParts[2] . '.%';
                
                // check by 1.1.1.*
                $count = $acDao->abuseCountLike( $ipSearch, date('Y-m-d H:i:s', strtotime('-'.fail2ban_max_attempts_network_timespan().' minutes')) );
                
                if ($count >= $maxAttemptsNetwork ) {
                    return 'Too many attempts from your network';
                }
            }
            // TODO: ipv6
        }
        
        return false;
    }
    
    
    public function cleanUp( $filter ) {
        $acDao = new BanCheckDAO();
        
        $filter = str_replace( '*', '%', trim($filter) );
        
        $acDao->deleteByFilter( $filter );
    }
    
    
}


