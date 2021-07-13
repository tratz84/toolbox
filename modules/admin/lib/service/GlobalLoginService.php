<?php

namespace admin\service;

use core\service\ServiceBase;
use admin\model\Autologin;
use admin\model\AutologinDAO;
use admin\model\CustomerDAO;

class GlobalLoginService extends ServiceBase {
    
    public function createRememberMe($contextName, $username, $ip) {
        $uniqKey = md5(uniqid().uniqid().uniqid().uniqid().uniqid().uniqid()).sha1(uniqid().uniqid().uniqid().uniqid().uniqid());
        
        $a = new Autologin();
        $a->setContextName($contextName);
        $a->setSecurityString($uniqKey);
        $a->setUsername($username);
        $a->setIp($ip);
        
        if ($a->save()) {
            return $a->getAutologinId().':'.$uniqKey;
        } else {
            return false;
        }
    }
    
    public function deleteAutologin($contextName, $username) {
        $aDao = new AutologinDAO();
        
        return $aDao->deleteByUsername($contextName, $username);
    }
    
    public function readAutologin($autologinId, $securityString) {
        $aDao = new AutologinDAO();
        
        $l=$aDao->readBySecurityString($autologinId, $securityString);
        
        if (count($l)) {
            return $l[0];
        } else {
            return null;
        }
    }

    public function deleteAutologinBySecurityString($autologinId, $securityString) {
        $t = $this->readAutologin($autologinId, $securityString);
        
        if ($t) {
            return $t->delete();
        }
        
        return false;
    }
    
    
    public function contextExists($contextName) {
        
        $cDao = new CustomerDAO();
        
        $customer = $cDao->readByName($contextName);
        if ($customer) {
            return $customer;
        } else {
            return false;
        }
    }
}

