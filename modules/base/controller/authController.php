<?php

use admin\service\GlobalLoginService;
use base\service\UserService;
use core\Context;
use core\controller\BaseController;
use core\exception\AuthenticationException;
use base\util\ActivityUtil;

class authController extends BaseController {
    
    
    
    public function action_index() {
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
                        ActivityUtil::logActivityUser($user->getUserId(), $user->getUsername(), 'auth-failure', 'Aanmelding vanaf niet toegestaan IP-adres', 'Succesvolle aanmelding, gebruiker: ' .$user->getUsername(). ', ip-adres: ' . remote_addr());
                        
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
    
    
    
    
}


