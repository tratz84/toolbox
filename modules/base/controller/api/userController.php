<?php



use base\service\UserService;
use core\controller\BaseController;
use base\util\ActivityUtil;

class userController extends BaseController {
    
    public function action_index() {
        $userService = $this->oc->get(UserService::class);
        
        $users = $userService->readAllUsers();
        
        $response = array();
        $response['users'] = $this->objectsToArray($users, array('user_id', 'username', 'email', 'user_type', 'firstname', 'lastname'));
        
        $this->json($response);
    }
    
    
    public function action_autologin() {
        
        $userService = $this->oc->get(UserService::class);
        $user = $userService->readByUsername($_REQUEST['username']);
        
        if (!$user) {
            return $this->json(array('error' => 'User not found'));
        }
        
        if (get_var('log-activity')) {
            ActivityUtil::logActivity(null, null, 'admin-auto-login', 0, 'admin-auto-login', 'Login door super-admin (als: '.$user->getUsername().')');
        }
        
        
        $token = $userService->generateAutologinToken($user->getUserId());
        
        $this->json(array('token' => $token));
    }
    
    
}
