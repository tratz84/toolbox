<?php

namespace core\filter;



use base\service\UserService;
use core\Context;
use core\ObjectContainer;
use core\exception\InvalidStateException;

class AuthFilter {
    
    
    public function __construct() {
        
    }
    
    
    public function doFilter($filterChain) {
        $ctx = Context::getInstance();
        
        
        // admin autologin token?
        if (isset($_GET['admin_autologin'])) {
            $userService = ObjectContainer::getInstance()->get(UserService::class);
            
            $user = $userService->readByAutologinToken($_GET['admin_autologin']);
            if ($user) {
                $_SESSION['user_id'] = $user->getUserId();
                $_SESSION['contextName'] = Context::getInstance()->getContextName();
                $_SESSION['admin_autologin'] = true;
                redirect('/');
            }
        }
        
        // public controller?
        if (strpos($ctx->getController(), 'public/') === 0) {
            return $filterChain->next();
        }
        
        // API call.. handle with json response
        if (strpos($ctx->getController(), 'api/') === 0) {
            if (isset($_SERVER['HTTP_API_KEY']) && $_SERVER['HTTP_API_KEY'] == API_KEY) {
                return $filterChain->next();
            }
            
            // TODO: basic authentication username:password support?
            
            $r = array();
            $r['message'] = 'Unknown API key';
            print json_encode($r);
            exit;
        }
        
        if (isset($_SESSION['user_id']) == false && strpos($ctx->getController(), 'json/') === 0) {
            if (isset($_SERVER['PHP_AUTH_USER']) && isset($_SERVER['PHP_AUTH_PW'])) {
                $userService = ObjectContainer::getInstance()->get(UserService::class);
                $user = $userService->readByUsername($_SERVER['PHP_AUTH_USER']);
                if ($user != null && $user->checkPassword($_SERVER['PHP_AUTH_PW'])) {
                    $_SESSION['user_id'] = $user->getUserId();
                    $ctx->setUser( $user );
                }
            }
            
            if (!$ctx->getUser()) {
                print json_encode(array('error' => 'Invalid username/password'));
                exit;
            }
        }
        
        if (isset($_SESSION['user_id']) == false) {
            $ctx->setModule('base');
            $ctx->setController('auth');
            if (in_array(get_var('a'), array('logo'))) {
                $ctx->setAction(get_var('a'));
            } else {
                $ctx->setAction('index');
            }
        } else {
            $userService = ObjectContainer::getInstance()->get(UserService::class);
            $user = $userService->readUser($_SESSION['user_id']);
            if ($user == null) {
                session_destroy();
                
                redirect('/');
            }
            
            $ctx->setUser($user);
        }
        
        
        
        // shouldn't happen. Here to prevent unseen bugs/hacks
        if (isset($_SESSION['contextName']) && $ctx->getContextName() != $_SESSION['contextName']) {
            throw new InvalidStateException('Wrong context set');
        }
        
        
        $filterChain->next();
    }
    
}

