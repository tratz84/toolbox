<?php



use core\controller\BaseController;
use fastsite\service\WebmenuService;
use fastsite\model\Webmenu;
use fastsite\form\WebmenuForm;

class webmenuController extends BaseController {
    
    
    
    public function action_index() {
        
        
        return $this->render();
    }
    
    
    public function action_edit() {
        
        $webmenuService = $this->oc->get(WebmenuService::class);
        
        if (get_var('id')) {
            $webmenu = $webmenuService->readMenu( get_var('id') );
        } else {
            $webmenu = new Webmenu();
        }
        
        $this->form = new WebmenuForm();
        $this->form->bind($webmenu);
        
        if (is_post()) {
            $this->form->bind( $_REQUEST );
            
            if ($this->form->validate()) {
                
                redirect('/?m=fastsite&c=webmenu');
            }
        }
        
        $this->isNew = $webmenu->isNew();
        
        return $this->render();
    }
    
    public function action_delete() {
        
        
    }
    
    
}



