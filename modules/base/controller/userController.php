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
    
    
    
    
    public function action_select2() {
        
        $userService = object_container_get(UserService::class);
        
        $r = $userService->search(0, 20, $_REQUEST);
        
        
        $arr = array();
        
        if (isset($_REQUEST['name']) == false || trim($_REQUEST['name']) == '') {
            $arr[] = array(
                'id' => '0',
                'text' => t('Make your choice'),
            );
        }
        foreach($r->getObjects() as $user) {
            $arr[] = array(
                'id' => $user['user_id'],
                'text' => $user['username']
            );
        }
        
        
        $result = array();
        $result['results'] = $arr;
        
        $this->json($result);
        
    }
}


