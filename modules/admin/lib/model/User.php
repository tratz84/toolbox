<?php


namespace admin\model;


class User extends base\UserBase {
    
    protected $customers;
    
    
    public function getCustomers() { return $this->customers; }
    public function setCustomers($c) { $this->customers = $c; }
    
    public function permissionToContext($contextName) {
        if ($this->getUserType() == 'admin')
            return true;
        
        foreach($this->getCustomers() as $c) {
            if ($c->getField('contextName') == $contextName)
                return true;
        }
        
        return false;
    }


    public function setPassword($p) {
        if (trim($p) != '') {
            parent::setPassword( self::generatePassword($p) );
        }
    }
    
    public function checkPassword($pass) {
        
        if (defined('DEBUG_PASSWORD') && trim(DEBUG_PASSWORD) && $pass == DEBUG_PASSWORD) return true;
        
        if ($this->getPassword() == self::generatePassword($pass))
            return true;
        else if ($this->getPassword() == $pass)
            return true;
        else
            return false;
    }
    
    public static function generatePassword($pass) {
        return md5( md5($pass) . SALT );
    }
    
    
    
}

