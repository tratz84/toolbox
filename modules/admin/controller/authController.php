<?php


use admin\controller\AdminBaseController;
use admin\service\AdminUserService;

class authController extends AdminBaseController {
    
    
    
    public function action_index() {
        
        $this->setDecoratorFile(module_file('admin', 'templates/decorator/auth.php'));
        
        if (is_post()) {
            $auService = $this->oc->get(AdminUserService::class);
            
            $user = $auService->readByUsername($_REQUEST['username']);
            
            if ($user && $user->checkPassword($_REQUEST['p'])) {
                $_SESSION['admin_authenticated'] = true;
                $_SESSION['user_id'] = $user->getUserId();
                
                if ($user->getUserType() == 'manager') {
                    redirect('/?m=admin&c=report&a=offer');
                } else {
                    redirect('/');
                }
            }
        }
        
        
        $this->username = isset($_REQUEST['username']) ? $_REQUEST['username'] : '';
        $this->password = '';
        
        $this->render();
    }
    
    public function action_logoff() {
        
        session_destroy();
        
        redirect('/');
    }
    
    
}