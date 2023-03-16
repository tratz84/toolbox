<?php



use core\controller\BaseController;
use base\service\UserService;
use base\model\UserGroup;

class groupController extends BaseController {
    
    
    public function action_index() {
        
        
        
        return $this->render();
    }
    
    
    public function action_edit() {
        
        $id = get_var('id');
        $userService = object_container( UserService::class );
        
        if ($id) {
            $group = $userService->readGroup($id);
        }
        else {
            $group = new UserGroup();
        }
        
        
        
        
        
        return $this->render();
    }
    
    
    
}



