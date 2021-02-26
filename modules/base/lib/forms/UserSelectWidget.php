<?php

namespace base\forms;

use core\forms\DynamicSelectField;
use base\service\UserService;

class UserSelectWidget extends DynamicSelectField {
    
    protected $userDeleted = false;
    
    
    public function __construct($name='user_id', $defaultValue=null, $defaultText=null, $endpoint=null, $label=null) {
        
        if ($defaultText == null) $defaultText = t('Make your choice');
        if ($endpoint == null) $endpoint = '/?m=base&c=user&a=select2';
        if ($label == null) $label = t('User');
        
        parent::__construct($name, $defaultValue, $defaultText, $endpoint, $label);
        
        $this->addContainerClass('user-select-widget');
        
    }
    
    
    public function bindObject($obj) {
        parent::bindObject($obj);
        
        $this->userDeleted = false;
        
        $userId = null;
        
        if (is_object($obj) && method_exists($obj, 'getUserId')) {
            $userId = $obj->getUserId();
        }
        
        if (is_array($obj) && isset($obj['user_id'])) {
            $userId = $obj['user_id'];
        }
        
        if ($userId) {
            $userService = object_container_get(UserService::class);
            $user = $userService->readUser( $userId );
            
            if ($user) {
                $this->setDefaultText( (string)$user );
            }
            else {
                $this->userDeleted = true;
                $this->setDefaultText( 'user-'.$userId );
            }
        } else {
            $this->setDefaultText( t('Make your choice') );
        }
        
    }
    
    
    public function render() {
        if ($this->userDeleted) {
            $this->addContainerClass('user-deleted');
        }
        
        return parent::render();
    }
    
}


