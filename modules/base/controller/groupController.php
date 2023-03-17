<?php



use core\controller\BaseController;
use base\service\UserService;
use base\model\UserGroup;
use base\form\UserGroupForm;

class groupController extends BaseController {
    
    
    public function action_index() {
        hook_htmlscriptloader_enableGroup('jstree');
        
        $userService = object_container_get( UserService::class );
        
        $treeGroups = $userService->readGroupsAsTree();
        
        $this->groupTree = $this->treeGroup2Jstree( $treeGroups );
        
        
        return $this->render();
    }
    
    protected function treeGroup2Jstree( $groups ) {
        
        $items = array();
        
        foreach( $groups as $g ) {
            $i = array();
            $i['id'] = $g->getUserGroupId();
            $i['text'] = $g->getGroupName();
            $i['state'] = array();
            $i['state']['opened'] = true;
            
            if ($g->hasChildren()) {
                $i['children'] = $this->treeGroup2Jstree( $g->getChildren() );
            }
            
            $items[] = $i;
        }
        
        return $items;
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
                
                $group = $userService->saveGroup( $this->form );
                report_user_message( t('Changes saved') );
                
                redirect( '/?m=base&c=group&a=edit&id='.$group->getUserGroupId() );
            }
            
        }
        
//         var_export($optGroupItems);exit;
        
        return $this->render();
    }
    
}



