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
        
        for($x=0; $x < count($menus); $x++) {
            $m = $menus[$x];
            
            $last = $x == count($menus)-1 ? true : false;
            
            $html .= '<div class="menu-item menu-item-'.$x.'" data-menu-id="'.$m->getWebmenuId().'">';
                $html .= '<a href="'.appUrl('/?m=fastsite&c=webmenu&a=edit&id='.$m->getWebmenuId()).'">';
                $html .= '<span>'.esc_html($m->getLabel()) . '</span>';
                $html .= '</a>';
            $html .= '</div>';
            
            $children = $m->getChildren();
            if (count($children)) {
                $html .= $this->renderMenus($children);
            }
        }
        
        $html .= '</div>';
        
        return $html;
    }
    
    
    public function action_sort_item() {
        
        $ids = explode(',', $_REQUEST['ids']);
        $selectedId = (int)$_REQUEST['selectedId'];
        
        $newOrder = array();
        
        for($x=0; $x < count($ids); $x++) {
            
            if (get_var('direction') == 'up') {
                // up
                if ($x+1 < count($ids) && $ids[$x+1] == $selectedId) {
                    $newOrder[] = $ids[$x+1];
                    $newOrder[] = $ids[$x];
                    $x++;
                } else {
                    $newOrder[] = $ids[$x];
                }
            } else {
                // down
                if ($x+1 < count($ids) && $ids[$x] == $selectedId) {
                    $newOrder[] = $ids[$x+1];
                    $newOrder[] = $ids[$x];
                    $x++;
                } else {
                    $newOrder[] = $ids[$x];
                }
            }
        }
        
        $webmenuService = $this->oc->get(WebmenuService::class);
        $webmenuService->updateMenuSort( $newOrder );
        
        $this->json(array(
            'status' => 'OK',
            'success' => true
        ));
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



