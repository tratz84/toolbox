<?php


namespace base\model;

use core\db\DatabaseHandler;


class MultiuserLock extends base\MultiuserLockBase {

    
    public function __construct($id=null) {
        parent::__construct($id);
        
    }
    
    
    
    public function save() {
        
        $this->setCreated(date('Y-m-d H:i:s'));
        
        $sql = "insert into base__multiuser_lock (username, tabuid, lock_key, ip, created) values (?, ?, ?, ?, ?) ON DUPLICATE KEY UPDATE lock_key = ?, created = ?";
        
        $params = array();
        $params[] = $this->getUsername();
        $params[] = $this->getTabuid();
        $params[] = $this->getLockKey();
        $params[] = $this->getIp();
        $params[] = $this->getCreated();
        $params[] = $this->getLockKey();
        $params[] = $this->getCreated();
        
        $con = DatabaseHandler::getConnection($this->resourceName);
        $result = $con->query($sql, $params);
        
        if ($result) {
            return true;
        }
        
        return false;
    }

}

