<?php


namespace base\service;


use core\service\ServiceBase;
use base\model\MultiuserLockDAO;
use base\model\MultiuserLock;

class MultiuserService extends ServiceBase {
    
    
    public function resetLock($username, $tabuid=null) {
        $mDao = new MultiuserLockDAO();
        
        $ml = $mDao->readByTab($username, $tabuid);
        
        if ($ml) {
            $ml->setLockKey(null);
            $ml->save();
        }
    }
    
    public function setLock($username, $tabuid=null, $lockKey) {
        $mDao = new MultiuserLockDAO();
        
        $ml = $mDao->readByTab($username, $tabuid);
        
        if (!$ml) {
            $ml = new MultiuserLock();
            $ml->setUsername($username);
            $ml->setTabuid($tabuid);
            
        }
        
        $ml->setLockKey($lockKey);
        $ml->setIp(remote_addr());
        $ml->save();
    }
    
    public function lockKeyCount($lockKey) {
        $mlDao = new MultiuserLockDAO();
        
        $mlDao->cleanup();
        
        return $mlDao->lockCountByUsername($lockKey);
    }
    
}

