<?php

namespace core\security;

use core\exception\AuthorizationException;

class AuthorizationCheck {
    
    protected $user;
    
    // 
    protected $module        = null;
    protected $controller    = null;
    protected $action        = null;
    
    protected $object        = null;
    
    protected $hasPermission = false;
    
    public function __construct( $user ) {
        $this->setUser($user);
    }
    
    
    public function getUser() { return $this->user; }
    public function setUser($u) { $this->user = $u; }
    
    public function allowPermission() { $this->hasPermission = true; }
    
    public function getModule() { return $this->module; }
    public function setModule($module) { $this->module = $module; }
    
    public function getController() { return $this->controller ; }
    public function setController($module) { $this->controller = $module; }
    
    public function getAction() { return $this->action; }
    public function setAction($action) { $this->action = $action; }
    
    public function setObject($obj) { $this->object = $obj; }
    public function getObject() { return $this->object; }
    
    public function hasAuthorization() {
        hook_eventbus_publish($this, 'core', 'authorization-check');
        
        return $this->hasPermission;
    }

    public function checkAuthorization() {
        if ($this->hasAuthorization() == false) {
            throw new AuthorizationException('No authorization');
        }
    }
    
}
