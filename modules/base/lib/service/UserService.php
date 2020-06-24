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
use base\model\ResetPassword;
use core\exception\ObjectNotFoundException;
use base\model\ResetPasswordDAO;
use core\exception\SecurityException;
use base\util\ActivityUtil;
use webmail\mail\SendMail;
use webmail\model\Email;
use webmail\model\EmailTo;
use core\db\DatabaseHandler;



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
    
    
    public function applyResetPassword($resetPassword, $newPassword) {
        
        // check if user still exists
        $user = $this->readUser( $resetPassword->getUserId() );
        if ($user == null) {
            throw new ObjectNotFoundException('User not found');
        }
        
        // mark link as used
        $resetPassword->setUsed( date('Y-m-d H:i:s') );
        $resetPassword->setUsedIp( remote_addr() );
        $resetPassword->save();
        
        // update password
        $pw = User::encryptPassword($newPassword);
        
        $uDao = object_container_get(UserDAO::class);
        $uDao->setPassword($user->getUserId(), $pw);
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
    
    
    
    public function readResetPassword($resetPasswordId, $securityString=null) {
        $rpDao = object_container_get(ResetPasswordDAO::class);
        
        $r = $rpDao->read( $resetPasswordId, $securityString );
        
        return $r;
    }
    
    
    public function resetPassword($userId) {
        
        $user = $this->readUser( $userId );
        if (!$user) {
            throw new ObjectNotFoundException('User not found');
        }
        
        $rpDao = object_container_get(ResetPasswordDAO::class);
        $cnt = $rpDao->resetPasswordCount( remote_addr(), 60 * 5 );
//         print $cnt;exit;
        if ($cnt > 5) {
            throw new SecurityException('Too many requests');
        }
        
        // can only send a pw request if user has a valid e-mailadres
        if (validate_email( $user->getEmail() ) == false) {
            ActivityUtil::logActivityUser($user->getUserId(), $user->getUsername(), 'password-request', 'Password requested, FAILED: no e-mail set');
            return;
        }
        
        // create reset-entry
        $rp = new ResetPassword();
        $rp->setUserId( $user->getUserId() );
        $rp->setUsername( $user->getUsername() );
        $rp->setSecurityString(md5(uniqid()).md5(uniqid()).md5(uniqid()).md5(uniqid()));
        $rp->setRequestIp( remote_addr() );
        $rp->save();
        
        
        // send e-mail
        $email = new Email();
        $email->setStatus(Email::STATUS_SENT);
        $email->setFromName('Toolbox - Admin');
        $email->setFromEmail('no-reply@localhost');
        $email->setSubject( t('Password reset requested for') . ' '  . $user->getUsername() );
        $email->setIncoming(false);
        
        $et = new EmailTo();
        $et->setToEmail( $user->getEmail() );
        $email->addRecipient( $et );
        
        $html = get_template( module_file('base', 'templates/auth/_reset_password_email.php'), [
            'reset_password_id' => $rp->getResetPasswordId(),
            'security_string'   => $rp->getSecurityString(),
            'ip'                => $rp->getRequestIp(),
            'username'          => $rp->getUsername()
        ]);
        $email->setTextContent( $html );
        $email->save();
        
        $sm = SendMail::createMail( $email );
        $sm->send();
        
        ActivityUtil::logActivityUser($user->getUserId(), $user->getUsername(), 'password-request', 'Password requested');
    }
    
    
    
    
}

