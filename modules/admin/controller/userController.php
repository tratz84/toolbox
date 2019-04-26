<?php



use admin\controller\AdminBaseController;
use admin\forms\AdminUserForm;
use admin\model\User;
use admin\service\AdminUserService;
use core\Context;
use core\exception\AuthorizationException;

class userController extends AdminBaseController {
    
    public function init() {
        parent::init();
        
        $user = Context::getInstance()->getUser();
        if ($user->getUserType() != 'admin') {
            throw new AuthorizationException('No authorization to user module');
        }
        
    }
    
    public function action_index() {
        $this->render();
    }
    
    
    
    public function action_search() {
        $pageNo = isset($_REQUEST['pageNo']) ? (int)$_REQUEST['pageNo'] : 0;
        $limit = 500;
        
        $userService = $this->oc->get(AdminUserService::class);
        
        $r = $userService->search($pageNo*$limit, $limit, $_REQUEST);
        
        $arr = array();
        $arr['listResponse'] = $r;
        
        
        $this->json($arr);
    }
    
    
    
    
    public function action_edit() {
        $userId = isset($_REQUEST['user_id']) ? (int)$_REQUEST['user_id'] : 0;
        
        $userService = $this->oc->get(AdminUserService::class);
        
        if ($userId) {
            $u = $userService->readUser($userId);
        } else {
            $u = new User();
        }
        
        $this->isNew = $u->isNew();
        $this->userForm = new AdminUserForm();
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
        $userService = $this->oc->get(AdminUserService::class);
        
        $userService->deleteUser($_REQUEST['user_id']);
        
        redirect('/?m=admin&c=user');
    }
    
}

