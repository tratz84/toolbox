<?php


namespace base\service;

use base\forms\UserForm;
use base\model\User;
use base\model\UserCapability;
use base\model\UserCapabilityDAO;
use base\model\UserDAO;
use base\user\UserCapabilityContainer;
use core\ObjectContainer;
use core\event\EventBus;
use core\forms\lists\ListResponse;
use core\service\ServiceBase;
use base\model\UserIpDAO;



class UserService extends ServiceBase {
    
    
    public function __construct() {
        
    }
    
    
    public function search($start, $limit, $opts=array()) {
        $fields = array('user_id', 'username', 'email', 'edited', 'created', 'user_type', 'firstname', 'lastname', 'fullname');
        
        return $this->daoSearch(UserDAO::class, $opts, $fields, $start, $limit);
    }
    
    
    public function readUser($userId) {
        $uDao = new UserDAO();
        
        $user = $uDao->read($userId);
        if (!$user)
            return null;
        
        $ucDao = new UserCapabilityDAO();
        $capabilities = $ucDao->readByUser($user->getUserId());
        $user->setCapabilities($capabilities);
        
        $upDao = new UserIpDAO();
        $ips = $upDao->readByUser($user->getUserId());
        $user->setIps($ips);
        
        return $user;
    }
    
    public function saveUser(UserForm $form) {
        $userId = $form->getWidget('user_id')->getValue();
        
        if ($userId) {
            $user = $this->readUser($userId);
        } else {
            $user = new User();
        }
        
        // handle..
        $form->fill($user, array('username', 'password', 'user_type', 'email', 'firstname', 'lastname'));
        
        $user->save();
        
        
        $ucDao = new UserCapabilityDAO();
        
        $oldUserCapabilities = $ucDao->readByUser($user->getUserId());
        $userCapabilities = array();
        
        // save capabilities
        $capabilities = $form->getWidget('user-capabilities');
        $capabilityWidgets = $capabilities->getWidgets();
        $cnt=0;
        for($x=0; $x < count($capabilityWidgets); $x++) {
            $w = $capabilityWidgets[$x];
            
            $module_name = $w->getField('module_name');
            $capability_code = $w->getField('capability_code');
            
            if ($module_name == null || $capability_code == null) continue;
            
            if ($w->getValue()) {
                // reuse old objects
                if (isset($oldUserCapabilities[$cnt])) {
                    $c = $oldUserCapabilities[$cnt];
                    $cnt++;
                } else {
                    $c = new UserCapability();
                }
                
                $c->setUserId($user->getUserId());
                $c->setModuleName($module_name);
                $c->setCapabilityCode($capability_code);
                
                $userCapabilities[] = $c;
            }
        }
        
        $ucDao = new UserCapabilityDAO();
        $ucDao->mergeFormListMTO1('user_id', $user->getUserId(), $userCapabilities);
        
        
        $upDao = new UserIpDAO();
        $newIps = $form->getWidget('ips')->getObjects();
        $upDao->mergeFormListMTO1('user_id', $user->getUserId(), $newIps);
        
        return $user;
    }
    
    public function deleteUser($userId) {
        
        $ucDao = new UserCapabilityDAO();
        $ucDao->deleteByUser($userId);
        
        $uiDao = new UserIpDAO();
        $uiDao->deleteByUser($userId);
        
        $uDao = new UserDAO();
        $uDao->delete($userId);
        
    }

    
    public function readByAutologinToken($t) {
        $uDao = new UserDAO();
        $users = $uDao->readByAutologinToken($t);

        if (count($users) == 0)
            return null;
        
        $uDao->resetAutologinToken($t);
        
        list ($token, $time) = explode('$', $t);
        
        if (preg_match('/^\\d{14}$/', $time) == false) {
            return null;
        }
        
        if (((int)date('YmdHis') - (int)$time) > 5) {
            return null;
        }
        
        return $users[0];
    }
    
    
    public function readByUsername($u) {
        $uDao = new UserDAO();
        return $uDao->readByUsername($u);
    }
    
    
    public function readAllUsers() {
        $uDao = new UserDAO();
        
        $users = $uDao->readAll();
        
        return $users;
    }
    
    
    public function generateAutologinToken($user_id) {
        
        $token = md5(uniqid().uniqid().uniqid().uniqid().uniqid().uniqid().uniqid().uniqid());
        // set timestamp, token may only be used for 10 seconds..
        $token .= '$' . date('YmdHis');
        
        
        $uDao = new UserDAO();
        $uDao->setAutologinToken($user_id, $token);
        
        return $token;
    }
    
    
    public function getCapabilities() {
        
        /**
         * @var EventBus $eventBus
         */
        $eventBus = ObjectContainer::getInstance()->get(EventBus::class);
        
        $ucc = new UserCapabilityContainer();
        
        $eventBus->publishEvent($ucc, 'base', 'user-capabilities');
        
        return $ucc->getCapabilities();
        
    }
    
}

