<?php

use admin\service\GlobalLoginService;
use base\service\UserService;
use core\Context;
use core\controller\BaseController;
use core\exception\AuthenticationException;
use base\util\ActivityUtil;
use core\exception\SecurityException;

class authController extends BaseController {
    
    // show a warning if the default 'admin123' password is set for user 'admin'
    protected $showWarningDefaultAdminPassword = false;
    
    public function init() {
        // .. ?
        if (ctx()->getUser() && in_array()) {
            redirect('/');
        }
        
        $this->addTitle('Login');
    }
    
    
    public function action_index() {
        // reset pw page requested?
        if (get_var('a') == 'reset_password') {
            return $this->action_reset_password();
        }
        
        if (get_var('a') == 'reset_link') {
            return $this->action_reset_link();
        }
        
        $this->setDecoratorFile( lookupModuleFile('templates/decorator/auth.php') );
        
        $this->username = '';
        $this->password = '';
        
        $this->remembermeChecked = isset($_REQUEST['rememberme']) && $_REQUEST['rememberme'] ? true : false;
        
        if (is_get() && isset($_COOKIE['securityString']) && strpos($_COOKIE['securityString'], ':') !== false) {
            list($autologinId, $token) = explode(':', $_COOKIE['securityString'], 2);
            
            $globalLoginService = $this->oc->get(GlobalLoginService::class);
            $autologin = $globalLoginService->readAutologin($autologinId, $token);
            
            if ($autologin) {
                if ($autologin->getContextName() == Context::getInstance()->getContextName()) {
                    $userService = $this->oc->get(UserService::class);
                    $user = $userService->readByUsername($autologin->getUsername());
                    
                    if ($user) {
                        $_SESSION['user_id'] = $user->getUserId();
                        $_SESSION['contextName'] = Context::getInstance()->getContextName();
                        
                        redirect('/');
                    }
                }
            }
        }
        
        
        if (is_post()) {
            // handle authentication
            $userService = $this->oc->get(UserService::class);
            $user = $userService->readByUsername(get_var('username'));
            if ($user != null && $user->checkPassword(get_var('p'))) {
                
                // check if IP-adres is allowed
                if ($user->getUserType() != 'admin') {
                    // readByUsername doesn't read user_ip's & user_capabilities
                    $user = $userService->readUser($user->getUserId());
                    
                    if (count($user->getIps()) > 0 && $user->containsIp(remote_addr()) == false) {
                        ActivityUtil::logActivityUser(
                            $user->getUserId(), $user->getUsername(), 
                            'auth-failure', 
                            'Aanmelding vanaf niet toegestaan IP-adres', 'Succesvolle aanmelding, gebruiker: ' .$user->getUsername(). ', ip-adres: ' . remote_addr()
                        );
                        
                        show_error('Het is niet toegestaan vanaf het huidige ip-adres aan te melden. Adres: '.remote_addr());
                    }
                    
                }
                
                
                $_SESSION['user_id'] = $user->getUserId();
                $_SESSION['contextName'] = Context::getInstance()->getContextName();
                
                if ($this->remembermeChecked) {
                    $globalLoginService = $this->oc->get(GlobalLoginService::class);
                    $securityString = $globalLoginService->createRememberMe(Context::getInstance()->getContextName(), $user->getUsername(), remote_addr());
                    if ($securityString) {
                        setcookie('securityString', $securityString, time()+60*60*24*730, appUrl('/'));
                    }
                }
                
                redirect('/');
            }
            
            // failed? => set error
            $this->error = t('Invalid username or password');
            
            $this->username = get_var('username');
        }
        
        
        if (is_get() && $this->ctx->getContextName() == 'demo') {
            $this->username = 'demo';
            $this->password = 'demo123';
        }
        
        // check if default password is set. if yes, show warning
        if (is_get() && is_standalone_installation()) {
            $userService = $this->oc->get(UserService::class);
            $user = $userService->readByUsername('admin');
            if ($user && $user->getPassword() == 'admin123') {
                $this->showWarningDefaultAdminPassword = true;
            }
        }
        
        
        $this->logoFile = $this->ctx->getLogoFile();
        
        
        $this->render();
    }
    
    public function action_logo() {
        $f = $this->ctx->getLogoFile();
        
        if ($f) {
            $f = get_data_file($f);
            
            header('Content-type: ' . mime_content_type($f));
            
            readfile($f);
            exit;
        }
        
    }
    
    
    public function action_logoff() {
        
        $ctx = $this->oc->get(Context::class);
        
        // remove autologin entries
        if ($ctx->getUser()) {
            $username = Context::getInstance()->getUser()->getUsername();
            
            $globalLoginService = $this->oc->get(GlobalLoginService::class);
            
            $globalLoginService->deleteAutologin($ctx->getContextName(), $username);
        }
        
        
        
        session_destroy();
        
        redirect('/');
    }
    
    
    
    public function action_reset_password() {
        if (ctx()->isResetPasswordEnabled() == false) {
            throw new SecurityException('Requested link disabled');
        }
        
        $this->setDecoratorFile( lookupModuleFile('templates/decorator/auth.php') );
        $this->setTemplateFile( module_file('base', 'templates/auth/reset_password.php') );
        
        
        if (is_post()) {
            try {
                $us = object_container_get( UserService::class );
                
                $lr = $us->search(0, 10, ['username' => get_var('id')]);
                if ($lr->getRowCount() > 0) {
                    $arrUser = $lr->getObject(0);
                    
                    $us->resetPassword( $arrUser['user_id'] );
                }
                else {
                    $lr = $us->search(0, 10, ['email' => get_var('id')]);
                    if ($lr->getRowCount() > 0) {
                        // first hit, what is the best method..
                        $arrUser = $lr->getObject( 0 );
                        $us->resetPassword( $arrUser['user_id'] );
    
                        // or reset for all found users?...
    //                     foreach($lr->getObjects() as $arrUser) {
    //                         $us->resetPassword( $arrUser['user_id'] );
    //                         break;
    //                     }
                    }
                    
                }
            } catch(\Exception $ex) {
                $this->error = $ex->getMessage();
            }
            
        }
        
        return $this->render();
    }
    
    
    
    
    public function action_reset_link() {
        if (ctx()->isResetPasswordEnabled() == false) {
            throw new SecurityException('Requested link disabled');
        }
        
        $this->setDecoratorFile( lookupModuleFile('templates/decorator/auth.php') );
        $this->setTemplateFile( module_file('base', 'templates/auth/reset_link.php') );
        
        $userService = object_container_get( UserService::class );
        
        // validate id/uid
        $rp = $userService->readResetPassword( get_var('id') );
        if (!$rp || $rp->getSecurityString() !== get_var('uid')) {
            $this->error = t('Link expired');
            return $this->render();
        }
        // older then 30 minuts?
        else if ($rp->getAgeInSeconds() > 60*30) {
            $this->error = t('Link expired');
            return $this->render();
        }
        else if ($rp->getUsed()) {
            $this->error = t('Link already used');
        }
        
        // check if user is in system
        $user = $userService->readUser( $rp->getUserId() );
        if (!$user) {
            $this->error = t('User not found');
            return $this->render();
        }
        
        $this->username = $rp->getUsername();
            
        if (is_post()) {
            if (get_var('p1') != get_var('p2')) {
                $this->message = t('Passwords not equal');
            }
            else if (password_strength_check(get_var('p1')) == false) {
                $this->message = t('Password not strong enough: minimal 6 characters, lower- & upper-case and a number');
                
            }
            else {
                $userService->applyResetPassword( $rp, get_var('p1') );
                
                $this->success = true;
            }
        }
        
        
        
        return $this->render();
    }
    
}


