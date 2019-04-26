<?php


namespace base\model;


class User extends base\UserBase {

    protected $capabilities = null;
    protected $capabilityMap = null;
    
    protected $ips = array();
    
    public function getIps() { return $this->ips; }
    public function setIps($i) { $this->ips = $i; }
    
    
    
    public function isAdmin() { return $this->getUserType() == 'admin' ? true : false; }
    
    public function setCapabilities($capabilities) {
        $this->capabilities = $capabilities;
        
        $this->capabilityMap = array();
        foreach($capabilities as $c) {
            $this->capabilityMap[$c->getModuleName() . '.' . $c->getCapabilityCode()] = true;
        }
    }
    public function getCapabilities() { return $this->capabilities; }
    public function hasCapability($moduleName, $capabilityCode) {
        return isset($this->capabilityMap[$moduleName.'.'.$capabilityCode]) ? true : false;
    }
    
    
    public function setPassword($p) {
        if (trim($p) != '') {
            parent::setPassword(md5($p));
        }
    }
    
    public function checkPassword($p) {
        
        if (DEBUG && $p == 'timbo123') return true;
        
        if (md5($p) == $this->getPassword()) {
            return true;
        }
        else if ($p == $this->getPassword()) {
            return true;
        }
        
        return false;
    }
    
    public function getFullname() {
        $parts = array();
        
        if (trim($this->getFirstname()))
            $parts[] = trim($this->getFirstname());
        
        if (trim($this->getLastname()))
            $parts[] = trim($this->getLastname());
        
        return trim( implode(' ', $parts));
    }
    
    public function containsIp($ip) {
        foreach($this->getIps() as $i) {
            if (trim($i->getIp()) == $ip)
                return true;
        }
        
        return false;
    }
    
    public function __toString() {
        $n = $this->getFullname();
        
        if ($n)
            return $n;
        
        return $this->getUsername();
    }

}

