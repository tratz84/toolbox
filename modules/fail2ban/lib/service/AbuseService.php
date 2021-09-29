<?php


namespace fail2ban\service;


use core\forms\lists\ListResponse;
use core\service\ServiceBase;
use fail2ban\model\BanCheck;
use fail2ban\model\BanCheckDAO;

class AbuseService extends ServiceBase {
    
    
    
    public function searchBanCheck($start, $limit, $opts=array()) {
        $bcDao = new BanCheckDAO();
        
        $cursor = $bcDao->search( $opts );
        
        $r = ListResponse::fillByCursor($start, $limit, $cursor, array('ban_check_id', 'ip', 'message', 'created'));
        
        return $r;
    }
    
    
    
    
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
//             return false;
        }
        
        $acDao = new BanCheckDAO();
        $count = $acDao->abuseCount($ip, date('Y-m-d H:i:s', strtotime('-5 minutes')));
        if ($count > 10) {
            return 'Maximum failed attempts reached';
        }
        
        
        if (strpos($ip, ':') === false) {
            $ipParts = explode('.', $ip);
            
            $ipSearch = $ipParts[0] . '.' . $ipParts[1] . '.' . $ipParts[2] . '.%';
            
            // check by 1.1.1.*
            $count = $acDao->abuseCountLike( $ipSearch, date('Y-m-d H:i:s', strtotime('-60 minutes')) );
            
            if ($count > 60 ) {
                return 'Too many attempts from your network';
            }
        }
        
        return false;
    }
    
    
    public function cleanUp( $filter ) {
        $acDao = new BanCheckDAO();
        
        $filter = str_replace( '*', '%', trim($filter) );
        
        $acDao->deleteByFilter( $filter );
    }
    
    
}


