<?php



use core\controller\BaseController;
use fastsite\service\WebmenuService;
use fastsite\model\Webmenu;
use fastsite\form\WebmenuForm;

class webmenuController extends BaseController {
    
    
    
    public function action_index() {
        $webmenuService = $this->oc->get(WebmenuService::class);
        
        $this->menus = $webmenuService->readByParent(null, true);
        $this->controller = $this;
        
        return $this->render();
    }
    
    public function renderMenus($menus) {
        
        $html = '';
        $html .= '<div class="menu-container">';
        
        foreach($menus as $m) {
            $html .= '<div class="menu-item">';
            $html .= '<a href="/?m=fastsite&c=webmenu&a=edit&id='.$m->getWebmenuId().'"><span>'.esc_html($m->getLabel().' - ' . $m->getUrl()) . '</span></a>';
            $html .= '</div>';
            
            $children = $m->getChildren();
            if (count($children)) {
                $html .= $this->renderMenus($children);
            }
        }
        
        $html .= '</div>';
        
        return $html;
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
                
                $webmenuService->saveWebmenu( $this->form );
                
                redirect('/?m=fastsite&c=webmenu');
            }
        }
        
        $this->isNew = $webmenu->isNew();
        
        return $this->render();
    }
    
    public function action_delete() {
        
        
    }
    
    
}



