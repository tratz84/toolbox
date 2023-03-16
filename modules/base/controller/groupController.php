<?php



use core\controller\BaseController;
use base\service\UserService;
use base\model\UserGroup;
use base\form\UserGroupForm;

class groupController extends BaseController {
    
    
    public function action_index() {
        
        
        
        return $this->render();
    }
    
    
    public function action_edit() {
        
        $id = get_var('id');
        $userService = object_container_get( UserService::class );
        
        if ($id) {
            $this->group = $userService->readGroup($id);
        }
        else {
            $this->group = new UserGroup();
        }
        
        
        $this->form = new UserGroupForm();
        $this->form->bind( $this->group );
        
        if (is_post()) {
            $this->form->bind( $_REQUEST );
            
            if ($this->form->validate()) {
                
                
                
            }
            
        }
        
        
        
        return $this->render();
    }
    
    
    
}



