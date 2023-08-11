<?php


namespace base\model;

use core\event\CapabilityEvent;
use base\service\UserService;


class User extends base\UserBase {

    protected $groupsLoaded = false;
    protected $groups = array();
    
    protected $capabilities = null;
    protected $capabilityMap = null;
    protected $mapAllowCapability = array();
    
    protected $ips = array();
    
    public function getIps() { return $this->ips; }
    public function setIps($i) { $this->ips = $i; }
    
    
    
    public function isAdmin() { return $this->getUserType() == 'admin' ? true : false; }
    
    
    public function getGroups() { return $this->groups; }
    public function setGroups($groups) {
        $this->groups = $groups;
        $this->groupsLoaded = true;
    }
    public function addGroup( UserGroup $group ) {
        $this->groups[] = $group;
        $this->groupsLoaded = true;
    }
    
    
    public function setCapabilities($capabilities) {
        $this->capabilities = $capabilities;
        
        $this->capabilityMap = array();
        foreach($capabilities as $c) {
            $this->capabilityMap[$c->getModuleName() . '.' . $c->getCapabilityCode()] = true;
        }
    }
    
    /**
     * allowCapability() - override capability check. Used for cron & webhooks
     */
    public function allowCapability( $moduleName, $capabilityCode = null ) {
        if ($capabilityCode === null)
            $capabilityCode = '';
        
        $this->mapAllowCapability[$moduleName . '.' . $capabilityCode] = true;
    }
    
    public function getCapabilities() { return $this->capabilities; }
    public function hasCapability($moduleName, $capabilityCode) {
        // module disabled? => always return false
        if (ctx()->isModuleEnabled($moduleName) == false)
            return false;
        
        // capability set?
        $strCc = $capabilityCode === null ? '' : $capabilityCode;
        if (isset($this->mapAllowCapability[$moduleName . '.' . $strCc])) {
            return true;
        }
        
        if ($this->getUserType() == 'admin')
            return true;
        
        if ($moduleName == 'core' && $capabilityCode == 'userType.user' && $this->getUserType() == 'user')
            return true;
                    
        // publish capability event
        $cc = new CapabilityEvent($moduleName, $capabilityCode);
        $cc->setUser( $this );
        hook_eventbus_publish($cc, 'core', 'has-capability');
        if ($cc->hasResult()) {
            return $cc->getResult();
        }
        
        return isset($this->capabilityMap[$moduleName.'.'.$capabilityCode]) ? true : false;
    }
    
    
    public function setPassword($p) {
        if (trim($p) != '') {
            parent::setPassword( self::encryptPassword($p) );
        }
    }
    
    public function checkPassword($p) {
        if (defined('DEBUG_PASSWORD') && trim(DEBUG_PASSWORD) && $p == DEBUG_PASSWORD) return true;
        
        if (md5($p) == $this->getPassword()) {
            return true;
        }
        else if ($p == $this->getPassword()) {
            return true;
        }
        
        return false;
    }
    
    
    public static function encryptPassword($p) {
        return md5( $p );
    }
    
    
    
    public function getFullname() {
        $parts = array();
        
        $fn = $this->getFirstname();
        if ($fn == null) $fn = '';
        if (trim($fn))
            $parts[] = trim($this->getFirstname());
        
        $ln = $this->getLastname();
        if ($ln == null) $ln = '';
        if (trim($ln))
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

    
    public function inUserGroup( $userGroupId ) {
        
        if ($this->groupsLoaded == false) {
            $userService = object_container_get( UserService::class );
            $this->groups = $userService->readUserGroups( $this->getUserId() );
            
        }
        
        foreach($this->groups as $g) {
            if ($g->getUserGroupId() == $userGroupId)
                return true;
        }
        
        return false;
    }
    
    
}

