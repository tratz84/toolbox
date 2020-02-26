<?php

use base\forms\UserForm;
use base\model\User;
use base\service\UserService;
use core\controller\FormController;
use core\exception\InvalidStateException;

class userController extends FormController {
    
    
    public function init() {
        checkCapability('base', 'edit-masterdata');
        
        $this->varNameId = 'user_id';
        
        $this->formClass = UserForm::class;
        $this->objectClass = User::class;
        
        $this->serviceClass = UserService::class;
        $this->serviceFuncSearch = 'search';
        $this->serviceFuncRead   = 'readUser';
        $this->serviceFuncSave   = 'saveUser';
        $this->serviceFuncDelete = 'deleteUser';
        
        $this->addTitle(t('Master data'));
        $this->addTitle(t('Overview users'));
    }
    
}


