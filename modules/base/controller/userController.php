<?php

use base\forms\UserForm;
use base\model\User;
use base\service\UserService;
use core\controller\BaseController;
use core\exception\InvalidStateException;

class userController extends BaseController {
    
    
    public function init() {
        checkCapability('base', 'edit-masterdata');
    }
    
    public function action_index() {
        $this->render();
    }
    
    public function action_search() {
        $pageNo = isset($_REQUEST['pageNo']) ? (int)$_REQUEST['pageNo'] : 0;
        $limit = $this->ctx->getPageSize();
        
        $userService = $this->oc->get(UserService::class);
        
        $r = $userService->search($pageNo*$limit, $limit, $_REQUEST);
        
        $arr = array();
        $arr['listResponse'] = $r;
        
        
        $this->json($arr);
    }
    
    
    
    
    public function action_edit() {
        $userId = isset($_REQUEST['user_id']) ? (int)$_REQUEST['user_id'] : 0;
        
        $userService = $this->oc->get(UserService::class);
        
        if ($userId) {
            $u = $userService->readUser($userId);
        } else {
            $u = new User();
        }
        
        $this->isNew = $u->isNew();
        $this->userForm = new UserForm();
        $this->userForm->bind( $u );
        
        
        if (is_post()) {
            
            $this->userForm->bind( $_REQUEST );
            
            if ($this->userForm->validate()) {
                $userService->saveUser($this->userForm);
                
                redirect('/?m=base&c=user');
            }
        }
        
        $this->render();
    }
    
    
    public function action_delete() {
        $userId = isset($_REQUEST['user_id']) ? (int)$_REQUEST['user_id'] : 0;

        $u = new User($userId);
        if ($u->read() == false) {
            throw new InvalidStateException('User not found');
        }

        $userService = $this->oc->get(UserService::class);
        $userService->deleteUser($userId);
        
        redirect('/?m=base&c=user');
    }
    
    
    
}


