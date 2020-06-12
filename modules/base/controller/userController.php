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
    
    public function action_edit($opts = array()) {
        parent::action_edit(['render' => false, 'stay_after_save' => true]);
        
        
        if ($this->object->getPassword() != '') {
            $this->form->getWidget('password')->setPlaceholder( t('Password set') );
        } else {
            $this->form->getWidget('password')->setPlaceholder( t('No password set') );
        }
        
        
        return $this->render();
    }
    
}


