<?php



use base\form\UserGroupForm;
use base\model\UserGroup;
use base\service\UserService;
use core\controller\BaseController;

class groupController extends BaseController {
    
    
    public function action_index() {
        hook_htmlscriptloader_enableGroup('jstree');
        
        $userService = object_container_get( UserService::class );
        
        $treeGroups = $userService->readGroupsAsTree();
        
        $this->groupTree = $this->treeGroup2Jstree( $treeGroups );
        
        $this->groupsAvailable = count($treeGroups) > 0 ? true : false;
        
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
    
    
    
    public function action_group_summary() {
        
        $userService = object_container_get( UserService::class );
        $group_id = (int)get_var('group_id');
        
        $this->group = $group = $userService->readGroup( $group_id );
        
        // fetch parents for title
        $parentNames = array();
        $pugid = $group->getParentUserGroupId();
        while ($pugid) {
            $parent = $userService->readGroup( $pugid, ['null-not-found' => true] );
            if ($parent) {
                $parentNames[] = $parent->getGroupName();
                $pugid = $parent->getParentUserGroupId();
            }
            else {
                break;
            }
        }
        $parentNames = array_reverse($parentNames);
        
        
        $this->title = implode( ' >> ', $parentNames );
        if (!$this->title)
            $this->title = $group->getGroupName();
        else
            $this->title .= ' >> ' . $group->getGroupName();
        
        // fetch users in group
        $this->lrUsers = $userService->search(0, 99999, ['user_group_id' => $group_id]);
        
        
        $this->allCapabilities = $userService->getCapabilities( ['as_map' => true] );
        
        
        $this->setShowDecorator(false);
        
        return $this->render();
    }
    
    
    
    
    public function action_edit() {
        
        $id = (int)get_var('id');
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
    
    
    public function action_delete() {
        
        $id = (int)get_var('id');
        
        
        $userService = object_container_get( UserService::class );
        
        $userService->deleteGroup( $id );
        
        
        
        redirect('/?m=base&c=group');
    }
    
    
    public function action_select_table() {
        $userService = object_container_get(UserService::class);
        
        $opts = array();
        
        
        $result = array();
        $result['header_fields'] = array(
            'type'           => t('Type'),
            'name'           => t('Username / Groupname'),
            'fullname'       => t('Fullname'),
            'email'          => t('E-mail'),
        );
        
        if (get_var('q') && trim(get_var('q')) != '') {
            $opts['id'] = get_var('value');
        }
        
        $result['results'] = $userService->searchUserOrGroup( get_var('q'), $opts );
        
        $this->json($result);
        
    }
}



