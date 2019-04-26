<?php

namespace admin\service;

use admin\forms\AdminUserForm;
use admin\model\User;
use admin\model\UserDAO;
use core\forms\lists\ListResponse;
use core\service\ServiceBase;
use admin\model\UserCustomerDAO;
use admin\model\UserCustomer;

class AdminUserService extends ServiceBase {
    
    
    public function search($start, $limit, $opts=array()) {
        $uDao = new UserDAO();
        
        $cursor = $uDao->search($opts);
        
        $r = ListResponse::fillByCursor($start, $limit, $cursor, array('user_id', 'username', 'user_type', 'edited', 'created'));
        
        return $r;
    }
    
    public function readUser($userId) {
        $uDao = new UserDAO();
        $u = $uDao->read($userId);
        
        if (!$u)
            return null;
        
        $ucDao = new UserCustomerDAO();
        $customers = $ucDao->readByUser($u->getUserId());
        $u->setCustomers($customers);
        
        return $u;
    }
    
    public function readByUsername($username) {
        $uDao = new UserDAO();
        
        return $uDao->readByUsername($username);
    }
    
    
    public function readAllUsers() {
        $uDao = new UserDAO();
        
        return $uDao->readAll();
    }
    
    
    public function saveUser(AdminUserForm $form) {
        $userId = $form->getWidget('user_id')->getValue();
        
        if ($userId) {
            $user = $this->readUser($userId);
        } else {
            $user = new User();
        }
        
        // handle..
        $form->fill($user, array('username', 'password', 'user_type'));
        $user->save();
        
        
        $customerSelectionWidgets = $form->getWidget('insights-customers')->getWidgets();
        $ucDao = new UserCustomerDAO();
        $oldUserCustomers = $ucDao->readByUser($user->getUserId());
        
        $newUserCustomers = array();
        
        $cnt=0;
        for($x=0; $x < count($customerSelectionWidgets); $x++) {
            $w = $customerSelectionWidgets[$x];
            
            if ($w->getValue() == false)
                continue;
            
            if (isset($oldUserCustomers[$cnt])) {
                $uc = $oldUserCustomers[$cnt];
            } else {
                $uc = new UserCustomer();
            }
            
            
            $uc->setUserId($user->getUserId());
            $uc->setCustomerId($w->getField('customerId'));
            
            $newUserCustomers[] = $uc;
            $cnt++;
        }
        
        $ucDao->mergeFormListMTO1('user_id', $user->getUserId(), $newUserCustomers);
    }
    
    
    public function deleteUser($userId) {
        $ucDao = new UserCustomerDAO();
        $ucDao->deleteByUser($userId);
        
        $uDao = new UserDAO();
        $uDao->delete($userId);
    }
    
    
    public function changePasswordCurrentUser($pass) {
        $u = \core\Context::getInstance()->getUser();
        
        
        $hashedPass = User::generatePassword($pass);
        $uDao = new UserDAO();
        $uDao->updatePassword($u->getUserId(), $hashedPass);
    }
    
    
    
}


