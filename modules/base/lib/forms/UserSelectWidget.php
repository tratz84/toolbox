<?php

namespace base\forms;

use core\forms\DynamicSelectField;
use base\service\UserService;

class UserSelectWidget extends DynamicSelectField {
    
    
    public function __construct($name='user_id', $defaultValue=null, $defaultText=null, $endpoint=null, $label=null) {
        
        if ($defaultText == null) $defaultText = t('Make your choice');
        if ($endpoint == null) $endpoint = '/?m=base&c=user&a=select2';
        if ($label == null) $label = t('User');
        
        parent::__construct($name, $defaultValue, $defaultText, $endpoint, $label);
    }
    
    
    public function bindObject($obj) {
        parent::bindObject($obj);
        
        $userId = null;
        
        if (method_exists($obj, 'getUserId')) {
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
        } else {
            $this->setDefaultText( t('Make your choice') );
        }
        
    }
    
}


